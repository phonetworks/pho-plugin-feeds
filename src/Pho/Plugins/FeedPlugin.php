<?php

namespace Pho\Plugins;

use Pho\Kernel\Kernel;
use Pho\Kernel\AbstractPlugin;
use Pho\Plugins\Feed\Waterfall\GraphListener;
use GetStream\Stream\Client;
use Psr\Log\LoggerInterface;
use Pho\Lib\Graph\GraphInterface;

/**
 * A Feed Plugin for Pho Kernel
 * 
 * This implementation works with GetStream,
 * and is webstreams compatible.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class FeedPlugin extends AbstractPlugin
{

    protected $client;

    /**
     * Constructor
     */
    public function __construct(Kernel $kernel, string $api_key, string $api_secret)
    {
        $this->client = new Client($api_key, $api_secret);
        parent::__construct($kernel);
    }

    /**
     * Init
     * 
     * The Starting point of the Feeds Plugin
     * listener waterfall.
     * 
     * It starts here, 
     * * expands to GraphListener.
     * * which then expands to NodeListener and EdgeListener in the second step.
     *
     * @return void
     */
    public function init(): void
    {
        $this->logger()->info("Beginning the Feed Plugin");
        GraphListener::listen($this);
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function logger(): LoggerInterface
    {
        return $this->kernel->logger()->bare();
    }

    public function graph(): GraphInterface
    {
        return $this->kernel->graph();
    }

    public function space(): GraphInterface
    {
        return $this->kernel->space();
    }

    public function addActivity(): void
    {
        $feed = $this->client()->feed("wall", (string) $node->id());
        $data = [
            "actor"=>(string) $node->id(), // actor id
            "verb"=>"_construct", // edge
            "object"=>"", // object id
            "txt"=>NodeFeedGenerator::process($node), // custom field
        ];
        $feed->addActivity($data);
    }
}