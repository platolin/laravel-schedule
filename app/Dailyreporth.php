<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dailyreporth extends Model
{
    //    
    protected $table = 'dailyreporth';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
     /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql';
}
