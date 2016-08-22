<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payin extends Model
{
    //
    protected $table = 'payin';
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
