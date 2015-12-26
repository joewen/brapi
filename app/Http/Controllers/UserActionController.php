<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Request;

class UserActionController extends Controller {

	private $redis;


	function __construct() 
	{
		$this->redis = \App\Cache\CacheManager::GetRedisClient();
   	}

	public function Update()
	{
		$request = Request::instance();
    	$actions = json_decode($request->getContent());

    	foreach ($actions as $key => $value) {
    		$keyPrefix = "ua-$key";
			$this->redis->incrby($keyPrefix, $value);
    	}
	}
}