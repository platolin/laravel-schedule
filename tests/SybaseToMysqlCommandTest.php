<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SybaseToMysqlCommandTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    
    public function testExample()
    {
        Artisan::call('Sybase:mysql', [
            'table' => 'test',
            //'command_parameter_2' => 'value2',
        ]);

        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('test->ok',$resultAsText);

    }

    public function testTransformTocdrcus_del()
    {
		Artisan::call('Sybase:mysql', [
            'table' => 'cdrcus_del',            
        ]);    	
		$resultAsText = Artisan::output();
        $this->assertContains('cdrcus_del',$resultAsText);
    }

    public function testTransformTocdr_hosp()
    {
		Artisan::call('Sybase:mysql', [
            'table' => 'cdr_hosp',            
        ]);    	
		$resultAsText = Artisan::output();
        $this->assertContains('cdr_hosp',$resultAsText);

    }

}
