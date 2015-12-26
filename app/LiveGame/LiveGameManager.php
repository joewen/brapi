<?php namespace App\LiveGame;


class LiveGameManager {

	private $redis;

	  function __construct() 
	  {
		    $this->redis = \App\Cache\CacheManager::GetRedisClient();
   	}

      private function GetChampionKey($platform, $championId)
      {
         return "lg-$platform-$championId";
      }

      private function GetChampionCountKey($platform, $championId)
      {
         return "lg-$platform-$championId-count";
      }

      private function GetPlatformGamesKey($platform)
      {
         return "lg-$platform";
      }

      private function GetPlatformGamesCountKey($platform)
      {
         return "lg-$platform-count";
      }

      private function GetChampionSetKey($platform)
      {
         return "lg-set-$platform-champion";
      }

   	public function CacheLiveGame($game)
   	{   		
   		$platform = $game->platform_id;
   		$gameId = $game->game_id;

   		$redisKey = "lg-$platform-$gameId";


   		if($this->redis->exists($redisKey))
   		{
   			return;
   		}

   		$gameInJson = json_encode($game, JSON_NUMERIC_CHECK);
   		$this->redis->set($redisKey, $gameInJson); 
   		$this->redis->lpush($this->GetPlatformGamesKey($platform), $redisKey); 
         $this->redis->incr($this->GetPlatformGamesCountKey($platform));

   		$players = array_merge($game->teamOne, $game->teamTwo);
   		foreach ($players as $value) {
   			$cid = $value->championId;
   			$redisKey = $this->GetChampionKey($platform, $cid);
            $this->redis->sadd($this->GetChampionSetKey($platform), $redisKey);
   			$this->redis->lpush($redisKey, $redisKey); 
   			$champCountKey = $this->GetChampionCountKey($platform, $cid);
   			$this->redis->incr($champCountKey);
   		}
   	}


   	public function GetNewGames($platform,$count)
   	{   		
   		$redisKey = $this->GetPlatformGamesKey($platform);
   		$gameJsons = $this->redis->lrange($redisKey, 0, $count - 1);
   		$games = array();

   		foreach ($gameJsons as $value) {
   			array_push($games, json_decode($this->redis->get($value)));
   		}
   		return $games;
   	}


      private function RemoveOldChampionListItem($platform)
      {
         $champSet = $this->redis->smembers($this->GetChampionSetKey($platform));
         foreach ($champSet as $value) {
            $cCount = $this->redis->getset("$value-count", 0);
            $toRemove = $this->redis->lrange($value, $cCount , -1);
            $this->redis->ltrim($value, 0, $cCount - 1);
         }
      }

      public function RemoveOldGames($platform)
      {       
         $this->RemoveOldChampionListItem($platform);

         $platformCountKey = $this->GetPlatformGamesCountKey($platform);
         $pCount = $this->redis->getset($platformCountKey, 0);

         $platformKey = $this->GetPlatformGamesKey($platform);
         $toRemove = $this->redis->lrange($platformKey, $pCount , -1);
         $this->redis->ltrim($platformKey, 0, $pCount - 1);

         foreach ($toRemove as $value) {
            $this->redis->del($value);
         }
      }


}