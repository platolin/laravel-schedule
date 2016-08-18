<?php

namespace App\Console\Commands;

use DB;
use App\relmek\Db\sybase;
use App\relmek\Db\sybase\mis;
use Illuminate\Console\Command;
use File;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use Log;

class Sybasetest extends Command
{
    // 命令名稱
    protected $signature = 'Sybase:test {start_date} {end_date} ';

    // 說明文字
    protected $description = '[測試] sybase ';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        
        $eis_data = DB::connection('sybase-bi')->select("select cusno , yymm , itnbr , qty , shpamt from eis_cdrsal where shpdate  >= ?  and shpdate <= ?  and cuskindd like 'E%' and shpno like 'S%'  and itnbr like 'SAP%' ",[$this->argument('start_date') , $this->argument('end_date') ]);

        //dd($eis_data);
        $count = 0;
        foreach ($eis_data as $key => $value) {
            $count ++ ;
            //dd($value);
            //$it =  new RecursiveIteratorIterator(new RecursiveArrayIterator($value));
            //$insert_value = iterator_to_array($it, false); 
            $insert_value = array( $value->cusno, $value->yymm );
            $check_data = DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->get();

            if ( ! $check_data ){
                $sql_insert  =  "insert into eis_cusno_group5 ( cusno, yymm )  ";
                $sql_insert  .= " value ( ?,? ) ";
                DB::insert($sql_insert, $insert_value );
                $old_data = array( "sm01_qty" => 0 , "sm02_qty" => 0 ,"sm03_qty" =>0 ,"sm04_qty"=>0,"sm05_qty" =>0 ,
                                   "sm01_amt" => 0 , "sm02_amt" => 0 ,"sm03_amt" =>0 ,"sm04_amt"=>0,"sm05_amt" =>0 );
            }else{
                $old_data = (array)$check_data[0];
            }
            /*
            １．　購買　清潔產品客戶群（ＳＡＰ００４／ＳＡＰ０００５／ＳＡＰ１０１１／ＳＡＰ１０１４／ＳＡＰＤ００５）
            ２．　洗髮群（ＳＡＰ３００／ＳＡＰ３００９／ＳＡＰ３０１３／ＳＡＰ３０１１）
            ３．　護潔群（ＳＡＰＡ００１／ＳＡＰＡ００２／ＳＡＰＡ００３
            ４．　護膚群（ＳＡＰ２０１６／ＳＡＰＤ００６）
            ５．　臉部清潔（ＳＡＰ５００１／ＳＡＰ５００８／ＳＡＰ１００２／ＳＡＰ７００３
            */
            $sm01_array = array('SAP0004','SAP0005','SAP1011','SAP1014','SAPD005');
            $sm02_array = array('SAP3008','SAP3009','SAP3013','SAP3011');
            $sm03_array = array('SAPA001','SAPA002','SAPA003');
            $sm04_array = array('SAP2016','SAPD006');
            $sm05_array = array('SAP5001','SAP5008','SAP1002','SAP7003');
            
            if ( in_array( trim($value->itnbr), $sm01_array ) ){
                DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->update(['sm01_qty'=>($value->qty + $old_data['sm01_qty']),'sm01_amt'=>($value->shpamt + $old_data['sm01_amt']) ]);
            }
            if ( in_array( trim($value->itnbr), $sm02_array ) ){
                DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->update(['sm02_qty'=>($value->qty + $old_data['sm02_qty']),'sm02_amt'=>($value->shpamt + $old_data['sm02_amt']) ]);
            }
            if ( in_array( trim($value->itnbr), $sm03_array ) ){
                DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->update(['sm03_qty'=>($value->qty + $old_data['sm03_qty']),'sm03_amt'=>($value->shpamt + $old_data['sm03_amt']) ]);
            }
            if ( in_array( trim($value->itnbr), $sm04_array ) ){
                DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->update(['sm04_qty'=>($value->qty + $old_data['sm04_qty']),'sm04_amt'=>($value->shpamt + $old_data['sm04_amt']) ]);
            }
            if ( in_array( trim($value->itnbr), $sm05_array ) ){
                DB::table('eis_cusno_group5')
                ->where('cusno', '=', $value->cusno )
                ->where('yymm', '=', $value->yymm )                
                ->update(['sm05_qty'=>($value->qty + $old_data['sm05_qty']),'sm05_amt'=>($value->shpamt + $old_data['sm05_amt']) ]);
            }

        }
        Log::info('Sybase to mysql total count = ',[$count]); 
        $this->info('Sybase ->ok total : ' . $count  ) ;
        // 檔案紀錄在 storage/test.log
        $log_file_path = storage_path('sybase_eis.log');

        // 記錄當時的時間
        $log_info = [
            'date'=>date('Y-m-d H:i:s')
        ];

        // 記錄 JSON 字串
        $log_info_json = json_encode($log_info) . "\r\n";

        // 記錄 Log
        //File::append($log_file_path, $log_info_json);
    }
}