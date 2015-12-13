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
		$keyPrefix = "result-$platform-s";
		$this->redis->incr($keyPrefix);
	}

	public function Fail($platform)
	{
		$keyPrefix = "result-$platform-f";
		$this->redis->incr($keyPrefix);
	}
}