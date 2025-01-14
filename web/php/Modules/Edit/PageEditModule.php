<?php

namespace Wikidot\Modules\Edit;


use Ozone\Framework\Database\Criteria;
use Ozone\Framework\Database\Database;
use Ozone\Framework\SmartyModule;
use Wikidot\Utils\ProcessException;
use Wikidot\Utils\WDEditUtils;
use Wikidot\Utils\WDPermissionManager;
use Wikidot\Utils\WDStringUtils;
use Wikidot\Form;
use Wikidot\Form\Renderer;
use Wikijump\Services\Deepwell\Models\Page;

class PageEditModule extends SmartyModule
{

    protected static $AUTOINCREMENT_PAGE = 'autoincrementpage';

    public function build($runData)
    {

        $pl = $runData->getParameterList();
        $site = $runData->getTemp("site");

        $pageId = $pl->getParameterValue("page_id");

        $user = $runData->getUser();

        $userId = $runData->getUserId();
        if ($userId == null) {
            $userString = $runData->createIpString();
            $runData->contextAdd("anonymousString", $userString);
        }

        $db = Database::connection();
        $db->begin();

        if ($pageId === null || $pageId==='') {
            // means probably creating a new page
            // no context is needed
            $runData->sessionStart();
            $runData->contextAdd("newPage", true);

            // first create if a page not already exists!
            $unixName = $pl->getParameterValue("wiki_page");
            $unixName = WDStringUtils::toUnixName($unixName); // purify! (for sure)

            if (!$unixName) {
                throw new ProcessException(_("The page cannot be found or does not exist."), "no_page");
            }

            $page = Page::findSlug($site->getSiteId(), $unixName);
            if ($page !== null) {
                // page exists!!! error!
                throw new ProcessException(_("The page you want to create already exists. Please refresh the page in your browser to see it."));
            }

            // extract category name
            if (strpos($unixName, ':') != false) {
                // ok, there is category!
                $exp = explode(':', $unixName);
                $categoryName = $exp[0];
                $suggestedTitle = ucwords(str_replace("-", " ", $exp[1]));
            } else {
                // no category name, "_default" assumed
                $categoryName = "_default";
                $suggestedTitle = ucwords(str_replace("-", " ", $unixName));
            }

            $stitle = $pl->getParameterValue("title");
            if ($stitle) {
                $suggestedTitle = $stitle;
            }

            // now check for permissions!!!
            WDPermissionManager::instance()->hasPagePermission('create', $user);
            $runData->contextAdd("title", $suggestedTitle);

            /* Select available templates, but only if the category does not have a live template. */
            $templatePage = "$categoryName:_template";

            if ($templatePage && $form = Form::fromSource($templatePage->getSource())) {
                $runData->contextAdd("form", new Renderer($form));
            } elseif (!$templatePage || !preg_match('/^={4,}$/sm', $templatePage->getSource())) {
                /*
                $templatesCategory = CategoryPeer::instance()->selectByName("template", $site->getSiteId());

                if ($templatesCategory != null) {
                    $c = new Criteria();
                    $c->add("category_id", $templatesCategory->getCategoryId());
                    $c->addOrderAscending("title");
                    $templates = [null]; // TODO run query

                    $runData->contextAdd("templates", $templates);
                }
                */

                // check if there is a default template...
            } else {
                /* Has default template, try to populate the edit box with initial content. */
                $templateSource = $templatePage->getSource();
                $split = preg_split('/^={4,}$/sm', $templateSource);
                if (count($split) >= 2) {
                    /* Fine, there is some initial content. */
                    $templateSource = trim(preg_replace("/^.*?\n={4,}/s", '', $templateSource));
                } else {
                    $templateSource = '';
                }
                $runData->contextAdd('source', $templateSource);
            }


            $db->commit();
            return;
        }

        // now if editing an existing page...

        if (!$pageId || !is_numeric($pageId)) {
            throw new ProcessException(_("The page cannot be found or does not exist."), "no_page");
        }

        $page = Page::findIdOnly($pageId, true);
        if ($page === null || $page->getSiteId() !== $site->getSiteId()) {
            throw new ProcessException(_("The page cannot be found or does not exist."), "no_page");
        }

        $category = $page->getCategory();
        if ($category == null) {
            throw new ProcessException(_("Internal error - page category does not exist!!!"));
        }

        // now check for permissions!

        WDPermissionManager::instance()->hasPagePermission('edit', $user, $category, $page);

        // now check if form is defined

        $templatePage = "$categoryName:_template";

        if (preg_match('/^[^:]*:[^_]|^[^_:][^:]*$/', $page->slug)
            && $templatePage && $form = Form::fromSource($templatePage->getSource())
        ) {
            $form->setDataFromYaml($page->getSource());
            $runData->contextAdd("form", new Renderer($form));
        }

        // if session is not started - start it!
        $runData->sessionStart();

        // check for conflicts

        $runData->ajaxResponseAdd('page_revision_id', $page->getRevisionId());

        // keep the session - i.e. put an object into session storage not to delete it!!!
        $runData->sessionAdd("keep", true);

        $runData->contextAdd("source", $page->wikitext);
        $runData->contextAdd("title", $page->title);
        $runData->contextAdd("pageId", $page->page_id);

        $runData->ajaxResponseAdd("timeLeft", 15*60);

        $db->commit();
    }
}
