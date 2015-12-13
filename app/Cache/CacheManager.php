<?php namespace App\Cache;

use Predis;
class CacheManager {

	public static function GetRedisClient()
	{	
		return new Predis\Client();
	}
}