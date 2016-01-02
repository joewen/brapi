<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\LiveGame\LiveGameManager;

class LiveGameController extends Controller {


	public function GetGames($platform, $start)
	{
		$lgm = new LiveGameManager($platform);
		return json_encode($lgm->GetGames($start, 10));
	}

	public function GetGamesByChampionId($platform, $championId, $start)
	{
		$lgm = new LiveGameManager($platform);
		return json_encode($lgm->GetGamesByChampionId($championId, $start, 10));
	}

	public function Remove()
	{
		$lgm = new LiveGameManager('TW');
		return json_encode($lgm->RemoveOldGames());
	}

}