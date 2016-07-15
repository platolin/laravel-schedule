<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SybaseToBiCommandTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    
    public function testSybaseToBi_eis_cdrsalmnew()
    {
        Artisan::call('Sybase:BI', [
            'type' => 'eis_cdrsalmnew',
            //'command_parameter_2' => 'value2',
        ]);

        // If you need result of console output
        $resultAsText = Artisan::output();
        $this->assertContains('eis_cdrsalmnew',$resultAsText);

    }
}
