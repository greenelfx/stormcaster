<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WebTest extends TestCase
{
    /**
     * A basic check that web routes are working
     *
     * @return void
     */
    public function testHome()
    {
        $this->visit('/')
             ->see('stormcaster');
    }
}
