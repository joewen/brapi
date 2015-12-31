<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LiveGame\LiveGameManager;

class RemoveOldLiveGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RemoveOldLiveGames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old live games';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lgm = new LiveGameManager('TW');
        $lgm->RemoveOldGames();

        $lgm = new LiveGameManager('SG');
        $lgm->RemoveOldGames();

        $lgm = new LiveGameManager('ID1');
        $lgm->RemoveOldGames();
    }
}
