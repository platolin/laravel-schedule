<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SqlToEmailCommandTest extends TestCase
{
    use DatabaseTransactions;
    use MailTracking;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSqlToEmail()
    {
        Artisan::call('Sql:Email', [
            'ServerType' => 'Mysql',
            'SqlSyntax' => "select userno from secuser where pdepno ='MIS' ",
            'Email'		=> 'plato@relmek.com.tw',            
        ]);

 		$this->seeEmailWasSent()
             ->seeEmailSubject('Mysql secuser Mis data')
             ->seeEmailTo('plato@relmek.com.tw')             
             ->seeEmailContains('secuser');
        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('test->ok',$resultAsText);
    }
}
