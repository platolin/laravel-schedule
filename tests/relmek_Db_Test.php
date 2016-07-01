<?php

/**
 * Created by PhpStorm.
 * User: plato
 * Date: 2016/6/30
 * Time: 上午 9:12
 */
use App\relmek\Db\sybase\mis;
use App\relmek\Db\sybase\cdrhmas;
class relmek_Db_Test extends TestCase
{

    public function testSybaseDb()
    {
        $sybase_mis = new mis;
        $this->assertEquals('sybase',$sybase_mis['connection']);

        $sybase_cdrhmas = new cdrhmas;
        $this->assertEquals('sybase',$sybase_cdrhmas['connection']);
    }

}
