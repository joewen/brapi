<?php namespace App\LoLApis;


class BaronClientApi {

	private static function GetServerAddress($platform)
	{
		$redis = \App\Cache\CacheManager::GetRedisClient();
		return $redis->get("bc-$platform");
	}

	private static function BaronClientQuery($platform, $method, $paramInJson)
	{
		$content = FALSE;
		$platform = strtoupper($platform);
		$server = BaronClientApi::GetServerAddress($platform);
		if($server != null)
		{
			$url = $server  . $platform . '/' . $method;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramInJson);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
			$content = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
			if($content != FALSE)
			{
				$content = json_decode($content);
			}
		}
		return $content;
	}

	public static function GetChallengerLeague($platform, $queueType)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($queueType));
		$content = BaronClientApi::BaronClientQuery($platform, 'getChallengerLeague', $jsonDataEncoded);
		return $content;
	}

	public static function GetSummonerByName($platform, $name)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($name));
		$content = BaronClientApi::BaronClientQuery($platform, 'getSummonerByName', $jsonDataEncoded);
		return $content;
	}

	public static function GetAggregatedStats($platform, $accountId, $season)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($accountId, 'CLASSIC', $season));
		$content = BaronClientApi::BaronClientQuery($platform, 'getAggregatedStats', $jsonDataEncoded);
		return $content;
	}

	public static function GetAllPublicSummonerDataByAccount($platform, $accountId)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($accountId));
		$content = BaronClientApi::BaronClientQuery($platform, 'getAllPublicSummonerDataByAccount', $jsonDataEncoded);
		return $content;
	}

	public static function GetRecentGames($platform, $accountId)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($accountId));
		$content = BaronClientApi::BaronClientQuery($platform, 'getRecentGames', $jsonDataEncoded);
		return $content;
	}

	public static function GetAllLeaguesForPlayer($platform, $summonerId)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($summonerId) , JSON_NUMERIC_CHECK);
		$content = BaronClientApi::BaronClientQuery($platform, 'getAllLeaguesForPlayer', $jsonDataEncoded);
		return $content;
	}

	public static function GetLeagueForPlayer($platform, $summonerId, $queueType)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($summonerId, $queueType) , JSON_NUMERIC_CHECK);
		$content = BaronClientApi::BaronClientQuery($platform, 'getLeagueForPlayer', $jsonDataEncoded);
		return $content;
	}

	public static function RetrievePlayerStatsByAccountId($platform, $accountId, $season)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($accountId, $season), JSON_NUMERIC_CHECK);
		$content = BaronClientApi::BaronClientQuery($platform, 'retrievePlayerStatsByAccountId', $jsonDataEncoded);
		return $content;
	}

	public static function GetNamesBySummonerId($platform, $summonerIdsArray)
	{	
		$platform = strtoupper($platform);
		$jsonDataEncoded = json_encode(array($summonerIdsArray), JSON_NUMERIC_CHECK);
		$content = BaronClientApi::BaronClientQuery($platform, 'getSummonerNames', $jsonDataEncoded);
		return $content;
	}

	public static function GetCurrentGameByName($platform, $name)
	{	
		$platform = strtoupper($platform);
		$jsonData = array($name);
		$jsonDataEncoded = json_encode($jsonData);
		$content = BaronClientApi::BaronClientQuery($platform, 'retrieveInProgressSpectatorGameInfo', $jsonDataEncoded);

		if($content == FALSE)
		{
			return FALSE;
		}
		else
		{
			$platformGameLifecycleDTO = $content;
			$gameDTO = $platformGameLifecycleDTO->game;
			$playerCredentials = $platformGameLifecycleDTO->playerCredentials;

			$game = new \stdClass;
			$game->game_id = $playerCredentials->gameId;
			$game->platform_id = $platform;
			$game->observer_server_ip = $playerCredentials->observerServerIp;
			$game->observer_server_port = $playerCredentials->observerServerPort;
			$game->observer_encryption_key = $playerCredentials->observerEncryptionKey;
			$game->queue_type = $gameDTO->queueTypeName;
			$game->map_id = $gameDTO->mapId;

			$game->teamOne = array();
			$game->teamTwo = array();

			$playerChampionSelectionsIndex = array();
			foreach ( $gameDTO->playerChampionSelections->array as $value) 
			{
				$playerChampionSelectionsIndex[$value->summonerInternalName] = $value;
			}

			foreach ( $gameDTO->teamOne->array as $value) {
				$player = $playerChampionSelectionsIndex[$value->summonerInternalName];
				$player->accountId = $value->accountId;
				$player->summonerId = $value->summonerId;
				$player->profileIconId = $value->profileIconId;
				$player->index = $value->index;
				$player->summonerName = $value->summonerName;
				array_push($game->teamOne,  $playerChampionSelectionsIndex[$value->summonerInternalName]);
			}

			foreach ( $gameDTO->teamTwo->array as $value) {
				$player = $playerChampionSelectionsIndex[$value->summonerInternalName];
				$player->accountId = $value->accountId;
				$player->summonerId = $value->summonerId;
				$player->profileIconId = $value->profileIconId;
				$player->index = $value->index;
				$player->summonerName = $value->summonerName;
				array_push($game->teamTwo,  $playerChampionSelectionsIndex[$value->summonerInternalName]);
			}

			$game->teamOneBans = array();
			$game->teamTwoBans = array();

			foreach ( $gameDTO->bannedChampions->array as $value) {
				if($value->teamId == 100)
					array_push($game->teamOneBans, $value);
				else if($value->teamId == 200)
					array_push($game->teamTwoBans, $value);
			}

			$game->originalCaller = $name;
			return $game;
			//return view('match', ['match' => $game]);
			//return json_encode($game);
		}
	}
}