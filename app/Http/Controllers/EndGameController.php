<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Match;
use App\MatchPlayer;
use Request;

class EndGameController extends Controller {


  public function NewEndedGame($platform, $gameId)
  {
    $request = Request::instance();
    $jsonContent = $request->getContent();
    $url = "http://brapitw.moa.tw/endOfGameStatus/$platform/$gameId";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonContent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
  }

}