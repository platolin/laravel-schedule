<?php

use App\Payin;
use App\Dailyreporth;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RelmekMonthlyTest extends TestCase
{
	use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */

 //    public function testRelmekMonthly()
 //    {
 //    	Artisan::call('Relmek:Monthly', [
 //            'Datatype' 	=> 'Mysql',
 //            'tablename' => 'dailyreport',
 //            'yymm'		=> '201601',         
 //        ]);
 //        $this->assertTrue(true);
 //    }
    /**
	*  member events to payin
	*
    */
    // public function testRelmekMonthlyEvents()
    // {
    //     $start_date = (Carbon::now())->subMonths(2)->day(26)->toDateString();
    //     $end_date = (Carbon::now())->subMonths(1)->day(25)->toDateString();
    //     $yymm = (Carbon::now())->subMonths(1)->format('Ym');

    //     // $events = NEW events;
    //     // $events->enterdate = $end_date;
    //     // $events->mancode = 'S001';
    //     // $events->ottime = '17:45';
    //     // $events->save();

    //     Artisan::call('Relmek:Monthly', [
    //         'Datatype'  => 'Mysql',
    //         'tablename' => 'events',
    //         'yymm'      => $yymm,
    //     ]);

    //     $payin = Payin::where('userno','S001')->where('trdate' , $end_date)->select('otrtime')->first();        
    //     $this->assertEquals($payin->otrtime , '1745');
    // }

    public function testRelmekMonthlyOTCreportTocard()
    {
    	$start_date = (Carbon::now())->subMonths(2)->day(26)->toDateString();
    	$end_date = (Carbon::now())->subMonths(1)->day(25)->toDateString();
    	$yymm = (Carbon::now())->subMonths(1)->format('Ym');

    	$dailyreporth = NEW Dailyreporth;
    	$dailyreporth->enterdate = $end_date;
    	$dailyreporth->mancode = 'S001';
    	$dailyreporth->ottime = '17:45';
    	$dailyreporth->save();

    	Artisan::call('Relmek:Monthly', [
            'Datatype' 	=> 'Mysql',
            'tablename' => 'dailyreport',
            'yymm'		=> $yymm,
        ]);

		$payin = Payin::where('userno','S001')->where('trdate' , $end_date)->select('otrtime')->first();		
		$this->assertEquals($payin->otrtime , '1745');

    }

    public function testRelmekArmanphMonthly()
    {
    	
    	// *   move armanph , armanpd to armanph_history , armanpd_history over 2 month ago 
    	// *   delete armanph_history , armanpd_history over one year ago 
    		
    	DB::table('armanph')->insert( ['anpno' => 'W990101002',
    								   'depno' => '001',
    								   'mancode' => 'S000',
    								   'anpdat' => '2016/6/1',
    								   'anpstat' =>	'Y',
    								   'recway' => '1' ]);
    	DB::table('armanpd')->insert( ['anpno' => 'W990101002',
    								   'trseq' => 1,
    								   'trno' => 'ZZZZZZZZ',
    								   'cusno' => 'S000001',
    								   'recamt' =>	1000]);
		Artisan::call('Relmek:Monthly', [
            'Datatype' 	=> 'Mysql',
            'tablename' => 'armanph',
            'yymm'		=> '201608',
        ]);
		$this->seeInDatabase('armanph_history', [
        	'anpno' => 'W990101002'
    	]);
    	$this->seeInDatabase('armanpd_history', [
        	'anpno' => 'W990101002',
        	'trseq' => 1,
    	]);
    }
}
