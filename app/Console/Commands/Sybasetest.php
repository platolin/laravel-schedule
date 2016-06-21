<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;


class Sybasetest extends Command
{
    // 命令名稱
    protected $signature = 'Sybase:test';

    // 說明文字
    protected $description = '[測試] sybase ';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        //
        //$results = \DB::select("select * from secuser  ");
        $results = \DB::select("select address from invwh where wareh ='C001' ");
        // 檔案紀錄在 storage/test.log
        $log_file_path = storage_path('test.log');
        var_dump(@iconv("BIG5","UTF-8//IGNORE",$results[0]->address) );
        // 記錄當時的時間
        $log_info = [
            'date'=>date('Y-m-d H:i:s'),
            'result'=>@iconv("BIG5","UTF-8//IGNORE",$results[0]->address)
        ];

        // 記錄 JSON 字串
        $log_info_json = json_encode($log_info) . "\r\n";

        // 記錄 Log
        File::append($log_file_path, $log_info_json);
    }
}