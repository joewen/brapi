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

        foreach ($platforms as $p) {
            $result = new RecordingResult;
            $result->platform = $p;
            $successKey = $prefix . $p . '-s';
            $result->success = $redis->get($successKey);
            if($result->success == null)
                $result->success = 0;
            $failKey = $prefix . $p . '-f';
            $result->fail = $redis->get($failKey);
            if($result->fail == null)
                $result->fail = 0;
            $result->save();

            $redis->del($successKey);
            $redis->del($failKey);
        }

    }
}
