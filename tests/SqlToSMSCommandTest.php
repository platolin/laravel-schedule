<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SqlToSMSCommandTest extends TestCase
{
    use DatabaseTransactions;
    use MailTracking;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSqlToSMS()
    {
        Artisan::call('Sql:SMS', [
            'ServerType' => 'Mysql'
        ]);
 		
        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('SMS send->ok',$resultAsText);
    }

}
