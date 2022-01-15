<?php

namespace Tests;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * @throws Exception
     */
    public function __get($key)
    {
        if ($key === 'faker')
            return $this->faker;

        throw new Exception('Unknown Key Requested');
    }
}
