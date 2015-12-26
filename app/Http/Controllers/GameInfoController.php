<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\LoLApis\BaronClientApi;
use App\LiveGame\LiveGameManager;
use Request;
use App;

class GameInfoController extends Controller {


	public function GetGameByName($platform, $name)
	{
		$game = BaronClientApi::GetCurrentGameByName($platform, $name);
		if($game != false)
		{
			$lgm = new LiveGameManager;
			$lgm->CacheLiveGame($game);
			return json_encode($game , JSON_NUMERIC_CHECK);
		}
		App::abort(404);
	}

	public function GetGameBySummonerId($platform, $summonerId)
	{
		$summonerIds = array();
		array_push($summonerIds, $summonerId);

		$summonerNames = BaronClientApi::GetNamesBySummonerId($platform,$summonerIds)->array;

		if(count($summonerNames) == 1)
		{
			return $this->GetGameByName($platform, $summonerNames[0]);
		}

		App::abort(404);
	}
}