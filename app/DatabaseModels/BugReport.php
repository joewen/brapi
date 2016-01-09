<?php

namespace App\DatabaseModels;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bug_reports';
    protected $primaryKey = 'id';

}
