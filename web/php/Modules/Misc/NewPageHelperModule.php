<?php

namespace Wikidot\Modules\Misc;

use Ozone\Framework\SmartyModule;
use Wikidot\Utils\ProcessException;
use Wikidot\Utils\WDStringUtils;

class NewPageHelperModule extends SmartyModule
{

    public function build($runData)
    {

        $site = $runData->getTemp("site");

        $pl = $runData->getParameterList();
        $categoryName = trim($pl->getParameterValue("category", "MODULE"));

        $template=trim($pl->getParameterValue("template", "MODULE"));

        $format=trim($pl->getParameterValue("format", "MODULE"));

        $runData->contextAdd("categoryName", WDStringUtils::toUnixName($categoryName));

        if ($template) {
            $ta = explode(',', $template);
            $tp = array();
            foreach ($ta as $t) {
            //  for each of the suggested arrays
                $t = trim($t);
                if (!preg_match("/^template:/", $t)) {
                    throw new ProcessException(sprintf(_('"%s" is not in the "template:" category.'), $t), "not_template");
                }
                $page = Page::findSlug($site->getSiteId(), $t);
                if ($page == null) {
                    throw new ProcessException(sprintf(_('Template "%s" cannot be found.'), $t), "no_template");
                }
                $tp[] = $page;
            }

            if (count($tp)>1) {
                $runData->contextAdd("templates", $tp);
            }
            if (count($tp) == 1) {
                $runData->contextAdd("template", $tp[0]);
            }
        }

        // size of the field

        $fieldSize = $pl->getParameterValue("size", "MODULE");
        $style = $pl->getParameterValue("style", "MODULE");
        $buttonText = $pl->getParameterValue("button", "MODULE");

        if (!$fieldSize) {
            $fieldSize = 30;
        }

        $runData->contextAdd('size', $fieldSize);
        $runData->contextAdd('style', $style);
        $runData->contextAdd('button', $buttonText);

        // check if format is valid (vali regexp)
        $m = false;
        if ($format) {
            $m = @preg_match($format, 'abc');

            if ($m !== false) {
                $runData->contextAdd('format', $format);
            } else {
                $runData->contextAdd("formatError", $format);
            }
        }
    }
}
