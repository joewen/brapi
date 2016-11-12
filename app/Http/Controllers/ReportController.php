<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Request;
use App\DatabaseModels\BugReport;

class ReportController extends Controller {

	private $deviceId;

	public function UserReport($deviceId)
	{
		$this->deviceId = $deviceId;
		$this->WriteToDB(1);
	}

	public function CrashReport($deviceId)
	{
		$this->deviceId = $deviceId;
		$this->WriteToDB(2);
	}

	private function WriteToDB($type)
	{
		/*
		$request = Request::instance();

    	$report = new BugReport;
    	$report->type = $type;
    	$report->device_id = $this->deviceId;
    	$report->content = $request->getContent();
    	$report->save();
    	*/
	}
}