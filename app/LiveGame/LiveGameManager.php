<?php namespace App\LiveGame;


class LiveGameManager {

	private $redis;
	private $platform;

	public function __construct($platform) 
	{
		$this->platform = $platform;
		$this->redis = \App\Cache\CacheManager::GetRedisClient();
	}

	private function GetChampionKey($championId)
	{
		return $this->GetPlatformGamesKey() . "-" . $championId;
	}

	private function GetChampionCountKey($championId)
	{
		return $this->GetChampionKey($championId) . "-count";
	}

	private function GetPlatformGamesKey()
	{
		return "lg-" . $this->platform;
	}

	private function GetPlatformGamesCountKey()
	{
		return $this->GetPlatformGamesKey() . '-count';
	}

	private function GetChampionSetKey()
	{
		return "lg-" . $this->platform .  "-championSet";
	}

	public function CacheLiveGame($game)
	{   		
		$gameId = $game->game_id;
		$gameKey = "lg-" . $this->platform . "-$gameId";
		if($this->redis->exists($gameKey))
		{
			return;
		}

		$gameInJson = json_encode($game, JSON_NUMERIC_CHECK);
		$this->redis->set($gameKey, $gameInJson); 
		$this->redis->lpush($this->GetPlatformGamesKey(), $gameKey); 
		$this->redis->incr($this->GetPlatformGamesCountKey());

		$players = array_merge($game->teamOne, $game->teamTwo);
		foreach ($players as $value) {
			$cid = $value->championId;
			$redisKey = $this->GetChampionKey($cid);
			$this->redis->sadd($this->GetChampionSetKey(), $redisKey);
			$this->redis->lpush($redisKey, $gameKey); 
			$champCountKey = $this->GetChampionCountKey($cid);
			$this->redis->incr($champCountKey);
		}
	}


	public function GetGames($count)
	{   		
		$redisKey = $this->GetPlatformGamesKey();
		return $this->GetGamesFromQueue($redisKey, $count);
	}

	public function GetGamesByChampionId($championId, $count)
	{        
		$redisKey = $this->GetChampionKey($championId);
		return $this->GetGamesFromQueue($redisKey, $count);
	}

	private function GetGamesFromQueue($redisKey, $count)
	{
		$gameJsons = $this->redis->lrange($redisKey, 0, $count - 1);
		$games = array();

		foreach ($gameJsons as $value) {
			array_push($games, json_decode($this->redis->get($value)));
		}
		return $games;
	}


	private function RemoveGameFromList($redisKey, $removeGameData)
	{
		//計算總共有多少過時的對戰要被移除
		$total = $this->redis->llen($redisKey);
		$count = $this->redis->getset("$redisKey-count", 0);
		$toRemove = $this->redis->lrange($redisKey, $count , -1);
		if($count == 0)
		{
			$this->redis->del($redisKey);
		}
		else
		{
			$this->redis->ltrim($redisKey, 0, $count - 1);
		}
		$remain = $this->redis->llen($redisKey);

		echo "Removing queue $redisKey, total $total, remove " . ($total - $count) . ", remain $count" . PHP_EOL;
		if($count != $remain)
		{
			echo "[Warning] Except remain is not equal to truth remain! Except: " . ($total - $count) . PHP_EOL;
		}

		if($removeGameData === TRUE)
		{
			foreach ($toRemove as $k) 
			{
				$this->redis->del($k);
			}
		}
	}

	private function RemoveOldChampionListItem()
	{ 

		echo "Remove old games from champion queues" . PHP_EOL;
		//移除英雄序列中的過時對戰，先找出所有的英雄序列
		$champSet = $this->redis->smembers($this->GetChampionSetKey());   

		foreach ($champSet as $value) {
			//計算總共有多少過時的對戰要被移除
			$this->RemoveGameFromList($value, FALSE);
		}
	}

	public function RemoveOldGames()
	{       
		$this->RemoveOldChampionListItem();
		echo "Remove old games from game queue" . PHP_EOL;
		$this->RemoveGameFromList($this->GetPlatformGamesKey(), TRUE);
	}


}