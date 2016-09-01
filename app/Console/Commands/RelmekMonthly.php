<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Payin;
use App\Dailyreporth;
use Carbon\Carbon;
use Log;
use DB;
use Mail;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;

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
            case 'armanph':
                $this->armanph();
                break;
            case 'events':
                $this->events();
                break;
        }       
    }
    /*
    *   
    *   
    */  
    protected function events()
    {       
        $insert_count = 0 ;
        $settings = DB::table('events_monthly_settings')->where('yymm', $this->yymm)->get();
        if( ! isset($start_date)){
            //規則: 當日最大結束時間, 接著判斷其是否屬整天事件.
            $str = "SELECT '$this->yymm',A.pdepno, A.userno, A.cmp_date, 
                CASE WHEN A.allDay = 1 THEN '17:45:00' ELSE TIME(A.cmp_time) END AS end_time
            FROM
            (SELECT '$this->yymm',pdepno, userno, allDay, DATE(`end`) AS cmp_date, MAX(`end`) AS cmp_time
                FROM `events`
                WHERE `end` >= '".$this->start_26."' AND `end` <= '".$this->end_25."'
                GROUP BY '$this->yymm',pdepno, userno, cmp_date
                ORDER BY '$this->yymm',pdepno, userno, cmp_date) A";
            $rs = DB::select(DB::raw($str));
            //dd($rs);
            $payin = NEW Payin;
            foreach($rs as $key=>$value) {
                /**
                * 寫月結檔
                */
                $it =  new RecursiveIteratorIterator(new RecursiveArrayIterator($value));
                $insert_value = iterator_to_array($it, false);
                //dd($insert_value);
                $insert_count ++;
                $sql_insert = "INSERT INTO events_monthly( yymm, pdepno, userno, days, hours) VALUES( ?, ?, ?, ?, ?)"; 
                DB::insert($sql_insert, $insert_value);    
                /**
                * 更新下班時間, 沒有卡鐘資料的需要Insert。
                */ 
                //dd($value);
                if( $payin::where('trdate',$value->cmp_date )->where('userno',$value->userno)->exists() ) {
                    $otrtime = substr($value->end_time, 0, 2).substr($value->end_time, 3, 2);                   
                    $payin::where('userno', $value->userno)
                        ->where('trdate', $value->cmp_date)
                        ->update(['otrtime' => $otrtime ]);
                } else {                    
                    $otrtime = substr($value->end_time, 0, 2).substr($value->end_time, 3, 2);                    
                    $payin_new = NEW Payin;
                    $payin_new->userno = $value->userno;
                    $payin_new->trdate = $value->cmp_date ;
                    $payin_new->itrtime = $otrtime;
                    $payin_new->otrtime = $otrtime;
                    $payin_new->save();
                } 
            }
            DB::table('events_monthly_settings')
                ->where('id', 1)
                ->update(['yymm' => $this->yymm ]);
            Mail::raw('total raw '.$insert_count , function ($message)
            {                       
                $message->to('nicole@relmek.com.tw','nicole');
                $message->to('olivia@relmek.com.tw','olivia');
                $message->bcc('plato@relmek.com.tw','plato');
                $message->subject("業務日報表月結完畢-{$this->yymm} 月");
            });
            Log::info('Relmek  Monthly event  total count :'.$insert_count ,['YYMM :', $this->yymm ]); 
        }

    }
    /*
    *   move armanph , armanpd to armanph_history , armanpd_history over 2 month ago 
    *   delete armanph_history , armanpd_history over one year ago 
    */  
    protected function armanph()
    {
        $this->yymm = $this->argument('yymm');
        $start_date = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 4,2) ,'01')->subMonths(2)->toDateString();
        $end_date   = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 4,2) ,'01')->subMonths(1)->toDateString();
        $start_date2 = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 4,2) ,'01')->subMonths(14)->toDateString();
        $end_date2   = Carbon::createFromDate(substr($this->yymm, 0,4),substr($this->yymm, 4,2) ,'01')->subMonths(13)->toDateString();

        DB::beginTransaction();
        $sql_armanph = "insert into armanph_history select * from armanph where anpdat >= '$start_date' and anpdat < '$end_date' ";
        $sql_armanpd = "insert into armanpd_history select * from armanpd where anpno in (select anpno from armanph where anpdat >= '$start_date' and anpdat < '$end_date') ";
        DB::statement(DB::raw($sql_armanph));
        DB::statement(DB::raw($sql_armanpd));
        $del_armanpd = "delete from armanpd where anpno in (select anpno from armanph where anpdat >= '$start_date' and anpdat < '$end_date') ";
        $del_armanph = "delete from armanph where anpdat >= '$start_date' and anpdat < '$end_date' ";
        DB::statement(DB::raw($del_armanpd));
        DB::statement(DB::raw($del_armanph));
        $del_armanpdh = "delete from armanpd_history where anpno in (select anpno from armanph_history where anpdat >= '$start_date2' and anpdat < '$end_date2') ";
        $del_armanphh = "delete from armanph_history where anpdat >= '$start_date2' and anpdat < '$end_date2' ";
        DB::statement(DB::raw($del_armanpdh));
        DB::statement(DB::raw($del_armanphh));
        DB::commit();
        Log::info('Relmek armanph & armanpd Monthly to history ok  ',['YYMM :', $this->yymm ]); 
        $this->info('Relmek armanph & armanpd to history ok');
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
        Mail::raw('total raw '.$insert_count , function ($message)
        {                                   
            $message->to('olivia@relmek.com.tw','olivia');
            $message->bcc('plato@relmek.com.tw','plato');
            $message->subject("OTC 業務日報表月結完畢-{$this->yymm} 月");
        });
        Log::info('Relmek  Monthly dailyreport total count :'.$insert_count ,['YYMM :', $this->yymm ]); 
        $this->info('dailyreport total :' .$insert_count );
    }
}
