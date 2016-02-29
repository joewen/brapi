<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DatabaseModels\RecordingResult;

class UpdateRecordingResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateRecordingResults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write recording results data to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prefix = 'result-';
        $prefixLen = strlen($prefix);

        $redis = \App\Cache\CacheManager::GetRedisClient();
        $rows = $redis->keys($prefix . '*');


        $platforms = array();

        foreach ($rows as $r) {
            $p = substr($r, $prefixLen, strlen($r) - $prefixLen - 2);
            if(!in_array ($p, $platforms))
            {
                array_push($platforms, $p);
            }
        }

        $slackMsg = 'BaronReplays 近一小時錄製成功率 '. PHP_EOL . date('F jS, Y H:i:s') . PHP_EOL;
        foreach ($platforms as $p) {
            $result = new RecordingResult;
            $result->platform = $p;
            $successKey = $prefix . $p . '-s';
            $success = $redis->get($successKey);
            if($success == null)
               $success = 0;
            $result->success = $success;

            $failKey = $prefix . $p . '-f';
            $fail = $redis->get($failKey);
            if($fail == null)
               $fail = 0;
            $result->fail = $fail;

            $result->save();

            $redis->del($successKey);
            $redis->del($failKey);

            $slackMsg = $slackMsg . sprintf("[%~-3s]        成功 % 5d        失敗 % 5d        成功率 %.2f %%",$p, $success, $fail,  (floatval($success) / (floatval($success) + floatval($fail)) * 100)) . PHP_EOL;

        }

        $this->SendToSlack($slackMsg);
    }


    private function SendToSlack($msg)
    {

        $msgObj = array('text' => $msg);
        $msgJson = json_encode($msgObj);

        $url = 'https://hooks.slack.com/services/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';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $msgJson);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        echo $httpcode ;
    }
}
