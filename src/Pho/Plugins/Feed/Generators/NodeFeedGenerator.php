<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Plugins\Feed\Generators;

use Pho\Kernel\Foundation\ParticleInterface;

class NodeFeedGenerator {

    public static function process(ParticleInterface $particle): string
    {
        if(
            (null===($particle::FEED_SIMPLE)||empty($particle::FEED_SIMPLE))
          //  ||
          //  (null!==($node::FEED_AGGREGATED)&&!empty($node::FEED_AGGREGATED))
        ) 
            return "";
        $feed = (string) $particle::FEED_SIMPLE;
        if(preg_match_all("/%([^%]+)%/", $feed, $matches)) {
            //error_log(print_r($matches, true));
            foreach($matches[0] as $i=>$match) {
                $func = \sprintf("get%s", \ucfirst($matches[1][$i]));
              //  error_log(".".print_r($particle->toArray(), true));
                //error_log("..".$particle->id()->toString());
                //error_log("...".$func);
                //error_log("....".$particle->$func());
                //error_log(".....".$particle->getUsername());
                //error_log("......".$particle->attributes()->Username);
                $feed = \str_replace($match, $particle->$func(), $feed);
            }
        }
        return $feed;
    }
}