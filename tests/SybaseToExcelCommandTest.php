<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SybaseToExcelCommandTest extends TestCase
{
 	use DatabaseTransactions;
 	use MailTracking;
    /**
     * A basic test example.
     *
     * @return void
     */
    
    public function testSybaseToExcel_cdrhmas()
    {
    

    }

    public function testSybaseToExcel_eisdata()
    {
        Artisan::call('Sybase:excel', [
            'type' => 'eis_data',            
        ]);

 		$this->seeEmailWasSent()
             ->seeEmailSubject('Monthly eis data')
             ->seeEmailTo('plato@relmek.com.tw')             
             ->seeEmailContains('eis_data');
        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('eis_data->ok',$resultAsText);
    }
}
