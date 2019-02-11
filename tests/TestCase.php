<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate:refresh', [
            '--seed' => '1'
        ]);

        // Artisan::call('migrate:refresh');
    }
}
