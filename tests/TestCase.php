<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Tests;

use Emeq\ItheorieApi\ItheorieServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('cache.default', 'array');
    }

    protected function getPackageProviders($app): array
    {
        return [
            ItheorieServiceProvider::class,
        ];
    }
}
