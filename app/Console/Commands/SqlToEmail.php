<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

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
        //
          Mail::raw('mysql secuser Mis data ', function ($message)
        {           
            $message->to($this->argument('Email'))->subject('Mysql secuser Mis data');
        });
        $this->info('test->ok');
    }
}
