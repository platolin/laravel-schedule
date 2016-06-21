<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Laravel 5')
             ->dontSee('Rails');

        $this->visit('/')
            ->click('About Us')
            ->seePageIs('about-us');
    }

    public function testNewUserRegistration()
    {
        $this->visit('/register')
            ->type('Taylor', 'name')
            ->check('terms')
            ->press('Register')
            ->seePageIs('/register');
    }

}

