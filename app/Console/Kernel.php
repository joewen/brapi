<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\UpdateLaunchStatistics::class,
        Commands\UpdateRecordingResults::class,
        Commands\UpdateUserActions::class,
        Commands\RemoveOldLiveGames::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('UpdateLaunchStatistics')
                 ->dailyAt('23:59');
        
        $schedule->command('UpdateRecordingResults')
                 ->hourly();

        $schedule->command('UpdateUserActions')
                 ->weeklyOn(0, '23:59');

        $schedule->command('RemoveOldLiveGames')
                 ->everyTenMinutes();
    }
}
