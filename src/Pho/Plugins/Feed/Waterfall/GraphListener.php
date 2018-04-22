<?php

namespace Pho\Plugins\Feed\Waterfall;

use Pho\Plugins\Feed\Waterfall\Exceptions\UnknownEntityException;
use Pho\Lib\Graph\GraphInterface;
use Pho\Lib\Graph\EntityInterface;
use Pho\Lib\Graph\NodeInterface;
use Pho\Lib\Graph\EdgeInterface;
use Psr\Log\LoggerInterface;
use Pho\Plugins\FeedPlugin;
use Pho\Framework\Actor;
use Pho\Plugins\Feed\Generators\NodeFeedGenerator;

class GraphListener
{
    public static function listen(FeedPlugin $plugin): void
    {
        
        $plugin->graph()->on("particle.formed", function($node) use ($plugin) {
            if($node instanceof Actor) {
                $observer = $plugin->client()->feed("timeline",  (string) $plugin->graph()->id());
                $observer->follow("wall", (string) $node->id());
                $feed = $plugin->client()->feed("wall", (string) $node->id());
                $data = [
                    "actor"=>(string) $node->id(), // actor id
                    "verb"=>"_construct", // edge
                    "object"=>(string) $node->id(), // object id
                    "txt"=>NodeFeedGenerator::process($node), // custom field
                ];
                $feed->addActivity($data);
            }
            NodeListener::listen($node, $plugin);
        });
        $members = $plugin->graph()->members();
        foreach($members as $member) {
            self::_($member, $plugin);
        }
    }

    /**
     * _
     * 
     * Listens Graph members, node, edge or anything.
     *
     * @param EntityInterface $member
     * 
     * @return void
     */
    protected static function _(EntityInterface $member, FeedPlugin $plugin): void
    {
        if($member instanceof NodeInterface) 
        {
            NodeListener::listen($member, $plugin);
            return;
        }
        elseif($member instanceof EdgeInterface)
        {
            EdgeListener::listen($member, $plugin);
            return;
        }
        throw new UnknownEntityException($member);
    }
} 