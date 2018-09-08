<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Plugins\Feed\Waterfall;

use Pho\Plugins\Feed\Waterfall\Exceptions\UnknownEntityException;
use Pho\Lib\Graph\EdgeInterface;
use Pho\Framework\ActorOut\Read;
use Pho\Framework\ActorOut\Subscribe;
use Pho\Framework\ActorOut\Write;
use Pho\Framework\ObjOut\Mention;
use Psr\Log\LoggerInterface;
use Pho\Plugins\FeedPlugin;

class EdgeListener
{
    public static function listen(EdgeInterface $edge, FeedPlugin $plugin): void
    {
        if($edge instanceof Read) {
            $plugin->logger()->info(sprintf("%s is a Read edge", (string) $edge->id()));
            return;
        }
        elseif($edge instanceof Write) {
            $plugin->logger()->info(sprintf("%s is a Write edge", (string) $edge->id()));
            return;
        }
        elseif($edge instanceof Subscribe) {
            $plugin->logger()->info(sprintf("%s is a Subscribe edge", (string) $edge->id()));
            return;
        }
        elseif($edge instanceof Mention) {
            $plugin->logger()->info(sprintf("%s is a Mention edge", (string) $edge->id()));
            return;
        }
        throw new UnknownEntityException($edge);
    }
}
