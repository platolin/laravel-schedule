<?php

namespace App\Console\Commands;

use DB;
use Mail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;

class SybaseToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sybase:mysql {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Sybase] Transform Sybase data to mysql';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $eMail ;
    protected $start_date;
    protected $end_date;

    public function __construct( Mail $mail)
    {
        parent::__construct();
        $this->eMail = $mail;
        // $this->start_date = (new Carbon('first day of this month'))->toDateString();
        // $this->end_date   = (new Carbon('first day of next month'))->toDateString();
        $this->start_date = (new Carbon('first day of last month'))->toDateString();
        $this->end_date   = (new Carbon('first day of this month'))->toDateString();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         Switch ($this->argument('table')){
            case 'otc_eis_cdrsal':
                $this->TransformToOtcEisCdrsal();
                break;
            case 'cdrcus_del':
                $this->TransformTocdrcus_del();
                break;
            case 'test':
                $this->TransformTest();
                break;
        }
    }
    /**
     * Execute the console command.
     * syabse delete cdrcus => mysql delete cdrcus & cdrcus 
     * @return 
     */
    public function TransformTocdrcus_del()
    {
        $cdrcus_del = DB::connection('sybase')->select("select cusno,kind,indate,cussta,status from cdrcus_del where status ='N' ");
        //dd($cdrcus_del);
        foreach ($cdrcus_del as $cdrcus_del_detial)
                {                    
                    DB::delete("delete from cdrscus where cusno = ? ", [$cdrcus_del_detial->cusno]);
                    DB::delete("delete from cdrcus where cusno = ? ", [$cdrcus_del_detial->cusno]);
                    DB::connection('sybase')->update("update cdrcus_del set status = ? where cusno = ? and kind = ? and indate = ? ",['D',$cdrcus_del_detial->cusno,$cdrcus_del_detial->kind,$cdrcus_del_detial->indate]);
                }

    }

    public function TransformToOtcEisCdrsal()
    {
        $trans_count = 0 ;
        DB::delete("delete from eis_cdrsal_ot where shpdate >=? and shpdate < ? ", [$this->start_date , $this->end_date]);
        DB::delete("delete from eis_cdrsalm_ot where shpdate >=? and shpdate < ? ", [$this->start_date , $this->end_date]);
        $sql_sybase  = "select shpdate,   cusno,   cusna,   mancode,   shpno,   wareh,   trseq,   itcls,   itnbr,   itdsc,   spdsc,   shpqy1,   shpamts,   yymm,   iocode,   depno,   cuskindd,   fdepno  from eis_cdrsal where shpdate >=  ? and shpdate < ?  and (fdepno = 'OT' OR mancode = '006') "; 
        $sql_sybase2  = "select shpdate,   cusno,   cusna,   mancode,   shpno,   wareh,   trseq,   itcls,   itnbr,   itdsc,   spdsc,   shpqy1,   shpamts,   yymm,   iocode,   depno,   cuskindd,   fdepno  from eis_cdrsalm where shpdate >=  ? and shpdate < ?  and (fdepno = 'OT' OR mancode = '006') "; 
    
        $eis_cdrsal = DB::connection('sybase-bi')->select($sql_sybase ,[$this->start_date , $this->end_date]);
        $eis_cdrsalm = DB::connection('sybase-bi')->select($sql_sybase2 ,[$this->start_date , $this->end_date]);
       
        foreach($eis_cdrsal as $key => $eis_detial){
            $eis_cdrsal_array[] = get_object_vars($eis_detial);            
        }        
        foreach($eis_cdrsalm as $key => $eis_detialm){
            $eis_cdrsalm_array[] = get_object_vars($eis_detialm);   
        }
        if( !empty($eis_cdrsal_array) ){
            foreach($eis_cdrsal_array as $key => $value){                        
                $value['shpdate'] = (new Carbon($value['shpdate']))->format('Y-m-d');
                $value['cusna'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['cusna'])) ;
                $value['itdsc'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['itdsc'])) ;
                $value['spdsc'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['spdsc'])) ;
                $it =  new RecursiveIteratorIterator(new RecursiveArrayIterator($value));
                $insert_value = iterator_to_array($it, false);            
                $sql_insert  =  "insert into eis_cdrsal_ot ( shpdate,   cusno,   cusna_utf8,   mancode,   shpno,   wareh,   trseq,   itcls,   itnbr,   itdsc_utf8,   spdsc_utf8,   shpqy1,   shpamts,   yymm,   iocode,   depno,   cuskindd,   fdepno)";
                $sql_insert  .= " value ( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )";
                DB::insert($sql_insert, $insert_value);
                $trans_count ++ ;
            }
        }
        if( !empty($eis_cdrsalm_array) ){
            foreach($eis_cdrsalm_array as $key => $value){                        
                $value['shpdate'] = (new Carbon($value['shpdate']))->format('Y-m-d');
                $value['cusna'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['cusna'])) ;
                $value['itdsc'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['itdsc'])) ;
                $value['spdsc'] = addslashes(@iconv("BIG5","UTF-8//IGNORE", $value['spdsc'])) ;
                $it =  new RecursiveIteratorIterator(new RecursiveArrayIterator($value));
                $insert_value = iterator_to_array($it, false);            
                $sql_insert  =  "insert into eis_cdrsalm_ot ( shpdate,   cusno,   cusna_utf8,   mancode,   shpno,   wareh,   trseq,   itcls,   itnbr,   itdsc_utf8,   spdsc_utf8,   shpqy1,   shpamts,   yymm,   iocode,   depno,   cuskindd,   fdepno)";
                $sql_insert  .= " value ( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )";
                DB::insert($sql_insert, $insert_value);
                $trans_count ++ ;
            }
        }
        Mail::raw('Sybase transform to Mysql ::eis_cdrsal_ot & eis_cdrsalm_ot OK total:' . $trans_count, function ($message)
        {                
            $message->to('plato@relmek.com.tw', 'plato')->subject('Sybase transform to Mysql : eis_cdrsal_ot');
        });
    }
    public function TransformTest()
    {        
        
        DB::delete("delete from eis_cdrsal_ot where shpdate >=? and shpdate < ? ", [$this->start_date , $this->end_date]);
        DB::delete("delete from eis_cdrsalm_ot where shpdate >=? and shpdate < ? ", [$this->start_date , $this->end_date]);
        
    }
}
