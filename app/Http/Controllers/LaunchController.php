<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class LaunchController extends Controller {

	public function Launch($brid)
	{
		$redis = \App\Cache\CacheManager::GetRedisClient();
		$redis->incr('launch-' . $brid);
	}
}