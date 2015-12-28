<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\LiveGame\LiveGameManager;

class LiveGameController extends Controller {


	public function GetGames($platform)
	{
		$lgm = new LiveGameManager($platform);
		return json_encode($lgm->GetGames(10));
	}

	public function GetGamesByChampionId($platform, $championId)
	{
		$lgm = new LiveGameManager($platform);
		return json_encode($lgm->GetGamesByChampionId($championId, 10));
	}

	public function Remove()
	{
		$lgm = new LiveGameManager('TW');
		return json_encode($lgm->RemoveOldGames());
	}

}