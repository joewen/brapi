<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class RecordingResultController extends Controller {

	private $redis;

	function __construct() 
	{
		$this->redis = \App\Cache\CacheManager::GetRedisClient();
   	}

	public function Success($platform)
	{
		$keyPrefix = "result-$platform-success";
		$this->redis->incr($keyPrefix);
	}

	public function Fail($platform)
	{
		$keyPrefix = "result-$platform-fail";
		$this->redis->incr($keyPrefix);
	}
}