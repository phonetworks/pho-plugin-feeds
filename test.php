<?php

require "vendor/autoload.php";

use Pho\Plugins\FeedPlugin;
use Pho\Kernel\Kernel;
use PhoNetworksAutogenerated\User;

$dotenv = new Dotenv\Dotenv(__DIR__."/vendor/phonetworks/pho-kernel");
$dotenv->load();

$_grapho = \PhoNetworksAutogenerated\Graph::class;
$_user_params = ["baba", "123456"]; // password only

if(class_exists(\PhoNetworksAutogenerated\Twitter::class)) 
{
    $_grapho = \PhoNetworksAutogenerated\Twitter::class;
}
elseif(class_exists(\PhoNetworksAutogenerated\Facebook::class))
{
    $_grapho = \PhoNetworksAutogenerated\Facebook::class;
}
elseif(class_exists(\PhoNetworksAutogenerated\Site::class))
{
    $_grapho = \PhoNetworksAutogenerated\Site::class;   
    $_user_params = ["the_founder", "x@y.org", "123456"]; 
}

$configs = array(
  "services"=>array(
      "database" => ["type" => getenv('DATABASE_TYPE'), "uri" => getenv('DATABASE_URI')],
      "storage" => ["type" => getenv('STORAGE_TYPE'), "uri" =>  getenv("STORAGE_URI")],
      "index" => ["type" => getenv('INDEX_TYPE'), "uri" => getenv('INDEX_URI')]
  ),
  "default_objects" => array(
  		"graph" => $_grapho,
  		"founder" => User::class,
  		"actor" => User::class
  )
);

$kernel = new \Pho\Kernel\Kernel($configs);
$founder = new \PhoNetworksAutogenerated\User($kernel, $kernel->space(), ...$_user_params);
$dotenv2 = new Dotenv\Dotenv(__DIR__);
$dotenv2->load();
$feedplugin = new FeedPlugin($kernel,  getenv('APP_ID'),  getenv('APP_SECRET'));
//$kernel->registerPlugin(FeedPlugin::class);
$kernel->registerPlugin($feedplugin);
$kernel->boot($founder);
eval(\Psy\sh());