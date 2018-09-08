<?php

namespace Pho\Plugins\Feed\Generators;

use Pho\Lib\Graph\EdgeInterface;

class EdgeFeedGenerator {

    public static function process(EdgeInterface $edge): string
    {
        if(
            (null===($edge::FEED_SIMPLE_LABEL)||empty($edge::FEED_SIMPLE_LABEL))
          //  ||
          //  (null!==($node::FEED_AGGREGATED)&&!empty($node::FEED_AGGREGATED))
        ) 
            return "";
        $feed = (string) $edge::FEED_SIMPLE_LABEL;
        if(preg_match_all("/%([^%]+)%/", $feed, $matches)) {
            foreach($matches[1] as $i=>$match) {
                if(strpos($match, ".")===false) {
                    $func = sprintf("get%s", ucfirst($match));
                    $feed = \str_replace($matches[0][$i], $edge->$func(), $feed);
                    continue;
                }
                $x = explode(".", $match, 2);
                $func = sprintf("get%s", ucfirst($x[1]));
                switch($x[0]) {
                    case "tail":
                        $feed = \str_replace($matches[0][$i], $edge->tail()->$func(), $feed);
                        break;
                    case "head":
                        $feed = \str_replace($matches[0][$i], $edge->head()->$func(), $feed);
                        break;
                    default:
                        break;
                }
                
            }
        }
        return $feed;
    }
}