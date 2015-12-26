<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Request;

class BaronClientController extends Controller {

	private $redis;
	private static $TTLSec = 900;

	function __construct() 
	{
		$this->redis = \App\Cache\CacheManager::GetRedisClient();
   	}

   	private function GetAddressByIPAndPort($ip, $port)
	{
		return "$ip:$port";
	}

	private function GetKey($platform)
	{
		return "bc-$platform";
	}
	
	public function Update($name, $platform, $port)
	{
		$this->redis->setex($this->GetKey($platform), self::$TTLSec, 'http://' . $this->GetAddressByIPAndPort(Request::getClientIp(true) , $port) . '/');
	}

	public function Down($name, $platform, $port)
	{
		$this->redis->del($this->GetKey($platform));
	}
}