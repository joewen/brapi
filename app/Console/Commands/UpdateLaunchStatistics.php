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

        $launchSetKey = 'la-set';

        $ls = new LaunchStatistic;
        $ls->date = $dateStr;
        $ls->count = $redis->getset('launch',0);
        $ls->count_distinct = $redis->scard($launchSetKey);
        $ls->save();

        $redis->del($launchSetKey);
    }
}
