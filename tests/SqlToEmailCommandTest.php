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
             ->seeEmailSubject('Sql Syntanx to email')
             ->seeEmailTo('plato@relmek.com.tw')             
             ->seeEmailContains('Mysql');
        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('test->ok',$resultAsText);
    }

    public function testSqlToEmail2()
    {
        Artisan::call('Sql:Email', [
            'ServerType' => 'pos',
            'SqlSyntax' => "select code , sdate , StartTime , EndTime , EndDate 
                            from HRS_AttendanceRecord , HRS_Employee where HRS_Employee.SerNo = HRS_AttendanceRecord.EmployeeSerNo  
                            and  sdate >= '20160401' and sdate <='20160410' ",
            'Email'     => 'plato@relmek.com.tw',            
        ]);

        $this->seeEmailWasSent()
             ->seeEmailSubject('Sql Syntanx to email')
             ->seeEmailTo('plato@relmek.com.tw')             
             ->seeEmailContains('pos');
        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('test->ok',$resultAsText);        
    }

}
