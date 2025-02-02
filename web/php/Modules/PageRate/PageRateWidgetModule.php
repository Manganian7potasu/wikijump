<?php

namespace Wikidot\Modules\PageRate;

use Ozone\Framework\Database\Criteria;
use Ozone\Framework\SmartyModule;
use Wikidot\DB\PageRateVotePeer;
use Wikijump\Services\Deepwell\Models\Page;

class PageRateWidgetModule extends SmartyModule
{
    public function build($runData)
    {
        $page = $runData->getTemp("page");
        if ($page) {
            $rate = $page->getRate();
        } else {
            $pl = $runData->getParameterList();
            $pageId = $pl->getParameterValue("pageId");
            if ($pageId) {
                $page = Page::findIdOnly($pageId);
                $rate = $page->getRate();
            } else {
                $rate = 0;
            }
        }

        $type = 'Z'; // TODO rating type
        $runData->contextAdd("type", $type);
        $runData->contextAdd("rate", $rate);

        // if the voting is average based (Stars), attach the count of votes for better display in the callback JS

        if ($type === "S") {
            $c = new Criteria();
            $c->add("page_id", $page->getPageId());

            $rates = PageRateVotePeer::instance()->select($c);
            $votecount = count($rates);
            $runData->contextAdd("votes", $votecount);
        }

        /**
         * If voting is disabled, send that to the widget so we don't display a
         * misleading widget. Used in PageRateWidgetModule.tpl
         */
        $runData->contextAdd("enabled", true);
    }
}
