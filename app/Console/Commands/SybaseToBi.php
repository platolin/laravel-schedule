<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SybaseToBi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sybase:BI {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Sybase]Conver Sybase Vproerp to BI';

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
        Switch ($this->argument('type')){
            case 'eis_cdrsalmnew':
                $this->Convereis_cdrsalmnew();
                break;
            case 'emmi-dent':
                $this->ConverFromEmmident();
                break;
            case 'test':
                $this->ConverFromTest();
                break;
        }
    }

    protected function Convereis_cdrsalmnew()
    {
        $start_date = (new Carbon('first day of last month'))->toDateString();
        $end_date   = (new Carbon('first day of this month'))->toDateString();
        DB::connection('sybase-bi')->statement('update_eis_cdrsalmnew ? , ? ', [$start_date , $end_date ]);        
    }
}
