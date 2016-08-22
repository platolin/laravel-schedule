<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Payin;
use App\Dailyreporth;
use Carbon\Carbon;
use Log;
class RelmekMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Relmek:Monthly {Datatype} {tablename} {yymm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Relmek] data monthly process ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $yymm;
    protected $start_26;
    protected $end_25;
    public function __construct()
    {
        parent::__construct();        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->yymm = $this->argument('yymm');
        $date1 = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 5,2) , 26);
        $date2 = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 5,2) , 25);
        $this->start_26 = $date1->subMonths(1)->toDateString();
        $this->end_25 = $date2->toDateString();

        Switch ($this->argument('tablename')){
            case 'dailyreport':
                $this->dailyreport();
                break;
        }       
       
    }

    protected function dailyreport()
    {
        $insert_count = 0 ;
        $dailyreporth = Dailyreporth::where('enterdate','>=',$this->start_26 )->where('enterdate','<=' , $this->end_25 )->get();
        $payin = NEW Payin;
        foreach ($dailyreporth as $key => $value) {
            $insert_count ++;
            $otrtime = str_replace(':','',$value->ottime);
            if( $payin::where('trdate',$value->enterdate )->where('userno',$value->mancode)->exists() )
            {
                $payin::where('userno', $value->mancode)
                ->where('trdate', $value->enterdate)
                ->update(['otrtime' => $otrtime ]);
            }
            else
            {
                $payin_new = NEW Payin;
                $payin_new->userno = $value->mancode;
                $payin_new->trdate = $value->enterdate ;
                $payin_new->itrtime = $otrtime;
                $payin_new->otrtime = $otrtime;
                $payin_new->save();            
            }
        } 
         if($insert_count > 0 )
        {
            Log::info('dailyreport Monthly to payin ok  ',['total trans :', $insert_count ]); 
        }

        $this->info('dailyreport total :' .$insert_count );
    }
}
