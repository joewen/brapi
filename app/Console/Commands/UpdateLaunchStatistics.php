<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DatabaseModels\LaunchStatistic;

class UpdateLaunchStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateLaunchStatistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write launch statistics data to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = getdate();
        $dateStr = $now['year'].'-'.$now['mon'].'-'.$now['mday'];

        $redis = \App\Cache\CacheManager::GetRedisClient();

        LaunchStatistic::where('date', $dateStr)->delete();

        $ls = new LaunchStatistic;
        $ls->date = $dateStr;
        $ls->count = $redis->getset('launch',0);
        $users = $redis->keys('brid-*');
        $ls->count_distinct = count($users);
        $ls->save();



        $pipe = $redis->pipeline();
        foreach ($users as $key) {
            $redis->del($key);
        }
        $res = $pipe->exec();

    }
}
