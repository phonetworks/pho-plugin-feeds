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
use Pho\Lib\Graph\NodeInterface;
use Pho\Framework\Actor;
use Pho\Framework\Graph;
use Pho\Framework\Obj;
use Psr\Log\LoggerInterface;
use Pho\Plugins\Feed\Generators\NodeFeedGenerator;
use Pho\Plugins\Feed\Generators\EdgeFeedGenerator;
use Pho\Plugins\Feed\Generators\JoinFeedGenerator;
use Pho\Plugins\FeedPlugin;
use Pho\Framework\ActorOut\Read;
use Pho\Framework\ActorOut\Subscribe;
use Pho\Framework\ActorOut\Write;
use Pho\Framework\ObjectOut\Mention;

class NodeListener
{
    public static function listen(NodeInterface $node, FeedPlugin $plugin): void
    {
        $id = (string) $node->id();
        if($node instanceof Actor) {
            $plugin->logger()->info(sprintf("%s is an Actor", $id));
            $node->on("edge.created", function($edge) use ($id, $plugin) {
                if($edge instanceof Write) {
                    $feed = $plugin->client()->feed("user",  $id);
                    $data = [
                        "actor"=>$id, // actor id
                        "verb"=>$edge->label(), // edge
                        "object"=>(string) $edge->head()->id(), // object id
                        "txt"=>EdgeFeedGenerator::process($edge), // custom field
                    ];
                    $feed->addActivity($data);
                }
                elseif($edge instanceof Subscribe) {
                    $feed = $plugin->client()->feed("user",  $id);
                    $observer = $plugin->client()->feed("timeline",  $id);
                    $observer->follow("user", (string) $edge->head()->id());
                    $data = [
                        "actor"=>$id, // actor id
                        "verb"=>$edge->label(), // edge
                        "object"=>(string) $edge->head()->id(), // object id
                        "txt"=>EdgeFeedGenerator::process($edge), // custom field
                    ];
                    $feed->addActivity($data);
                }
            });
            $node->on("joined", function($group) use ($node, $id, $plugin) {
                $feed = $plugin->client()->feed("user",  $id);
                $data = [
                    "actor"=>$id, // actor id
                    "verb"=>"join", // edge
                    "object"=>(string) $group->id(), // object id
                    "txt"=>JoinFeedGenerator::process($node, $group), // custom field
                ];
                $feed->addActivity($data);
            });
            return;
        }
        elseif($node instanceof Obj) {  
            $plugin->logger()->info(sprintf("%s is an Object", (string) $node->id()));
            // skip
            return;
        }
        elseif($node instanceof Graph) {
            $plugin->logger()->info(sprintf("%s is a Graph", (string) $node->id()));
            return;
        }
        throw new UnknownEntityException($node);
    }
}
