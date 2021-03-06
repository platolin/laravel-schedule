<?php
/**
 * Created by PhpStorm.
 * User: relmek
 * Date: 2016/6/30
 * Time: 下午 2:26
 */

namespace App\Console\Commands;

use DB;
use Mail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Console\Command;


class SybaseToExcel  extends Command
{
// 命令名稱
    protected $signature = 'Sybase:excel {type}';

    // 說明文字
    protected $description = '[Sybase] Conver sybase database to excel file';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        Switch ($this->argument('type')){
            case 'cdrhmas':
                $this->ConverFromCdrhmas();
                break;
            case 'emmi-dent':
                $this->ConverFromEmmident();
                break;
            case 'eis_data':
                $this->ConverFromEis_data();
                break;
            case 'test':
                $this->ConverFromTest();
                break;
        }
    }

    public function ConverFromEis_data()
    {
        $start_date = (new Carbon('first day of last month'))->toDateString();
        $end_date   = (new Carbon('first day of this month'))->toDateString();
        $eis_data = DB::connection('sybase')->select("select sum(shpamt+bakamt) as amt , sum(qty+bakqty) as qty ,yymm, fdepno, itnbr ,itdsc,spdsc from bi..eis_cdrsal where shpdate >= ? and shpdate <  ? and   itcls ='S' and shpno not like 'A%' group by yymm, fdepno, itnbr ,itdsc,spdsc ",[$start_date , $end_date]);
           
        Excel::create('eis_data', function($excel) use($eis_data) {
            $excel->sheet('Sheet1', function($sheet) use($eis_data){
                $eis_array= array();
                foreach ($eis_data as $eis_detail)
                {
                    $eis_detail->itdsc = addslashes(@iconv("BIG5","UTF-8//IGNORE", $eis_detail->itdsc)) ;
                    $eis_detail->spdsc = addslashes(@iconv("BIG5","UTF-8//IGNORE", $eis_detail->spdsc)) ;
                    $eis_array[] = get_object_vars($eis_detail);                
                }
                $sheet->fromArray($eis_array, null, 'A1', false);
            });
        })->store('csv');
        Mail::raw('eis_data 資料', function ($message)
        {
            $message->attach(storage_path().'/exports/eis_data.csv');            
            $message->to('jentang@relmek.com.tw', 'jentang')->subject('ERP Emmi-dent 資料');
            $message->to('plato@relmek.com.tw', 'plato')->subject('Monthly eis data');
        });
        $this->info('eis_data->ok')   ;
    }

    public function ConverFromEmmident()
    {
        $start_date = (new Carbon('first day of last month'))->toDateString();
        $end_date   = (new Carbon('first day of this month'))->toDateString();
        $emmident = DB::connection('sybase')->select("select shpdate,shpno,hmark1,depno,mancode,totamts,mark from cdrhad where shpdate >= ? and shpdate < ? and houtsta ='Y' and totamts = 7200 and invoiceyn = 'N' ",[$start_date , $end_date]);
        $emmident2 = DB::connection('sybase')->select("select bakdate,bakno,depno,mancode,totamts,trtype   from cdrbhad where bakdate >= ? and bakdate < ? and baksta  ='Y' and totamts = 7200 ",[$start_date , $end_date]);

        Excel::create('emmident', function($excel) use($emmident) {
            $excel->sheet('Sheet1', function($sheet) use($emmident){

                $emmi_array= array();
                foreach ($emmident as $emmident_detial)
                {
                    $emmi_array[] = get_object_vars($emmident_detial);
                }
                $sheet->fromArray($emmi_array);
            });

        })->store('csv');
        Excel::create('emmident2', function($excel) use($emmident2) {
            $excel->sheet('Sheet1', function($sheet) use($emmident2){

                $emmi_array= array();
                foreach ($emmident2 as $emmident_detial)
                {
                    $emmi_array[] = get_object_vars($emmident_detial);
                }
                $sheet->fromArray($emmi_array);
            });

        })->store('csv');

        Mail::raw('Emmi-dent 資料', function ($message)
        {
            $message->attach(storage_path().'/exports/emmident.csv');
            $message->attach(storage_path().'/exports/emmident2.csv');
            $message->to('jentang@relmek.com.tw', 'jentang')->subject('ERP Emmi-dent 資料');
            $message->to('plato@relmek.com.tw', 'plato')->subject('ERP Emmi-dent 資料');
        });
    }

    public function ConverFromTest()
    {
        $start_date = (new Carbon('yesterday'))->toDateString();
        $end_date   = (new Carbon('today'))->toDateString();
        $emmident = DB::connection('sybase')->select("select shpdate,shpno,hmark1,depno,mancode,totamts,mark from cdrhad where shpdate >= ? and shpdate < ? and houtsta ='Y' ",[$start_date , $end_date]);

        Excel::create('sybasetest', function($excel) use($emmident) {
            $excel->sheet('Sheet1', function($sheet) use($emmident){

                $emmi_array= array();
                foreach ($emmident as $emmident_detial)
                {
                    $emmi_array[] = get_object_vars($emmident_detial);
                }
                $sheet->fromArray($emmi_array);
            });

        })->store('xls');

        Mail::raw('Test 資料', function ($message)
        {
            $message->attach(storage_path().'/exports/sybasetest.xls');
            $message->to('plato@relmek.com.tw', 'plato')->subject('ERP test 資料');
        });
    }
    public function ConverFromCdrhmas()
    {
        $start_date = (new Carbon('first day of last month'))->toDateString();
        $end_date   = (new Carbon('first day of this month'))->toDateString();
        $cdrhmas = DB::connection('sybase')
            ->select('select substring(cuspono,1,1)as cp_EIP , cdrno,depno,mancode,cuycode,hrecsta,cusno,tramts,hmark1 from cdrhmas where recdate >= ? and recdate < ?  '
                ,[$start_date , $end_date]);
        Excel::create('cdrhmas', function($excel) use($cdrhmas) {

            $excel->sheet('Sheet1', function($sheet) use($cdrhmas){

                $cdrhmas_array= array();
                foreach ($cdrhmas as $cdrhmas_detail)
                {
                    $cdrhmas_array[] = get_object_vars($cdrhmas_detail);
                }
                $sheet->fromArray($cdrhmas_array);
            });

        })->store('xls');

        Mail::raw('ERP 訂單資料', function ($message)
        {
            $message->attach(storage_path().'/exports/cdrhmas.xls');
            $message->to('viola@relmek.com.tw', 'viola')->subject('ERP 訂單資料');
            $message->to('plato@relmek.com.tw', 'plato')->subject('ERP 訂單資料');

        });
    }
}