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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            DB::table('recent_users')->delete();
        })->daily();

        $schedule->command('cache:clear')
            ->Daily()
            ->sendOutputTo('/var2/www/html/laravel-Schedules/storage/logs')
            ->emailOutputTo('plato@relmek.com.tw');

        // 每分鐘執行 Artisan 命令 test:Log
        //$schedule->command('test:Log')->everyMinute();

    }
}
