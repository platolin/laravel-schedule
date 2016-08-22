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

    public function testRelmekMonthly()
    {
    	Artisan::call('Relmek:Monthly', [
            'Datatype' 	=> 'Mysql',
            'tablename' => 'dailyreport',
            'yymm'		=> '201601',         
        ]);

        $this->assertTrue(true);
    }
    /**
	*
	*
    */
    public function testRelmekMonthlyOTCreportTocard()
    {
    	$start_date = (Carbon::now())->subMonths(2)->day(26)->toDateString();
    	$end_date = (Carbon::now())->subMonths(1)->day(25)->toDateString();
    	$yymm = (Carbon::now())->subMonths(1)->format('Ym');
    	//dd($yymm);
        //$end_date   = (new Carbon('first day of this month'))->toDateString();		
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
}
