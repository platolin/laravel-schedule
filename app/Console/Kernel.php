<?php

namespace App\Console;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\TestLog::class,
        \App\Console\Commands\Sybasetest::class,
        \App\Console\Commands\SybaseToExcel::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // 每分鐘執行 Artisan 命令 test:Log
        //$schedule->command('test:Log')->everyMinute();->dailyAt('13:00');
        $schedule->command('Sybase:excel test')->dailyAt('14:00');

        $schedule->command('Sybase:excel emmi-dent')->monthlyOn(1, '15:50');

        $schedule->command('Sybase:excel cdrhmas')->monthlyOn(1, '15:51');

    }
}
