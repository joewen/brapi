<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('launch/{brid}', 'LaunchController@Launch');

Route::get('recording_result/success/{platform}', 'RecordingResultController@Success');

Route::get('recording_result/fail/{platform}', 'RecordingResultController@Fail');

Route::get('bc/update/{name}/{platform}/{port}', 'BaronClientController@Update');

Route::get('bc/down/{name}/{platform}/{port}', 'BaronClientController@Down');

Route::get('live/byName/{platform}/{name}', 'GameInfoController@GetGameByName');

Route::get('live/bySummonerId/{platform}/{name}', 'GameInfoController@GetGameBySummonerId');

Route::post('user/report', 'UserActionController@Update');

Route::get('version', function()
{
    return 222;	
});

Route::get('/', function()
{
    return Redirect::to('https://ahri.tw/en');	
});



Route::get('live/{platform}', 'LiveGameController@GetGames');

Route::get('live/byChampionId/{platform}/{championId}', 'LiveGameController@GetGamesByChampionId');