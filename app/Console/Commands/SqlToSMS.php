<?php

namespace App\Console\Commands;

use DB;
use Log;
use Illuminate\Console\Command;
use TwsmsSender\TwsmsSender;

class SqlToSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sql:SMS {ServerType} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[SQL] Sql syntax to SMS ';
    protected $username;
    protected $password;
    protected $sendtime;
    protected $SqlSyntax;
    protected $SmsMessage;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $config= parse_ini_file( base_path()."/twsms.ini",true) ;         
        $this->username = $config['twsms']['username'];
        $this->password = $config['twsms']['password'];
        $this->sendtime = $config['twsms']['sendtime'];
        $this->SqlSyntax = $config['sql']['text'];
        $this->SmsMessage = $config['message']['text'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $TwsmsSender = new TwSmsSender($this->username,$this->password);
        
        $skincode_vip = DB::connection('mysql')->select($this->SqlSyntax);
        $count = 0;
        foreach ($skincode_vip as $key => $value) {
        
            $result = $TwsmsSender->send( $value->tel3 , $this->SmsMessage.$value->telcode.$value->name , $this->sendtime);
        
            if ($result['text'] == 'Success')
            {
                $count ++;
                DB::table('cdrcus')
                ->where('cusno', $value->cusno)
                ->update(['sms_msgid' => $result['id']]);
            }
        }
        Log::info('Sql to SMS total sent message',[$count]); 
        $this->info('SMS send->ok') ;
    }

}
