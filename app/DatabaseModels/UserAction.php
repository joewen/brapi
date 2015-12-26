<?php

namespace App\DatabaseModels;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_actions';
    protected $primaryKey = 'id';

}
