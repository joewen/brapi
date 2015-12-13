<?php

namespace App\DatabaseModels;

use Illuminate\Database\Eloquent\Model;

class LaunchStatistic extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'launch_statistics';
    protected $primaryKey = 'date';

}
