<?php
/**
 * Created by PhpStorm.
 * User: relmek
 * Date: 2016/6/30
 * Time: 上午 11:08
 */

namespace App\relmek\Db\sybase;


use Illuminate\Database\Eloquent\Model;

class cdrhmas extends Model
{

    protected $connection = 'sybase';
    
    protected $primaryKey = 'facno , cdrno';

}