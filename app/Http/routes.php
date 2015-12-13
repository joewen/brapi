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

Route::get('/', function () {
    return view('welcome');
});


Route::get('launch/{brid}', 'LaunchController@Launch');

Route::get('recording_result/success/{platform}', 'RecordingResultController@Success');

Route::get('recording_result/fail/{platform}', 'RecordingResultController@Fail');