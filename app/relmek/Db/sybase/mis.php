<?php
/**
 * Created by PhpStorm.
 * User: plato
 * Date: 2016/6/30
 * Time: 上午 9:42
 */

namespace App\relmek\Db\sybase;
//use Illuminate\Database\Eloquent\Model;
use Eloquent;

class mis extends Eloquent
{

    protected $connection = 'sybase';

    protected $primaryKey = 'company';
    protected $fillable = ['facno'];
}
