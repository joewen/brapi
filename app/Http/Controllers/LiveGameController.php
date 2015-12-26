<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\LiveGame\LiveGameManager;

class LiveGameController extends Controller {


	public function GetNewNames()
	{
		$lgm = new LiveGameManager;
		return json_encode($lgm->GetNewGames('TW', 10));
	}

	public function Remove()
	{
		$lgm = new LiveGameManager;
		return json_encode($lgm->RemoveOldGames('TW'));
	}

}