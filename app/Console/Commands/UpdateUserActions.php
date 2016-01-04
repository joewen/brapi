<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DatabaseModels\UserAction;

class UpdateUserActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateUserActions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write user actions data to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redis = \App\Cache\CacheManager::GetRedisClient();

        $actions = $redis->keys('ua-*');

        foreach ($actions as $key) {
            $count = $redis->getset($key, 0);
            if($count != 0)
            {
                $ua = new UserAction;
                $ua->action = substr($key, 3);
                $ua->count = $redis->getset($key, 0);
                $ua->save();
            }
        }

    }
}
