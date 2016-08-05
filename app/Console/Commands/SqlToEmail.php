<?php

namespace App\Console\Commands;

use DB;
use Mail;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class SqlToEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sql:Email {ServerType} {SqlSyntax} {Email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[SQL] select SqlSyntax to excel and email to user ';

    protected $ServerType ;
    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        Switch ($this->argument('ServerType')){
            case 'Mysql':
                $ServerType = 'mysql';
                break;
            case 'pos':
                $ServerType = 'pos-sql';
                break;
            case 'sybase':
                $ServerType = 'sybase';
                break;
            case 'sybase-bi':
                $ServerType = 'sybase-bi';
                break;
        }
        $sql_data = DB::connection($ServerType)->select($this->argument('SqlSyntax'));
        //dd($sql_data);
        Excel::create('sql_data', function($excel) use($sql_data) {
            $excel->sheet('Sheet1', function($sheet) use($sql_data){
                $sql_array= array();
                foreach ($sql_data as $sql_detail)
                {               
                    $sql_array[] = get_object_vars($sql_detail);                
                }
                $sheet->fromArray($sql_array, null, 'A1', false);
            });
        })->store('csv');
        Mail::raw('sql to email '.$this->argument('ServerType') , function ($message)
        {           
             $message->attach(storage_path().'/exports/sql_data.csv');
            $message->to($this->argument('Email'))->subject('Sql Syntanx to email');
        });
        $this->info('test->ok');
    }
}
