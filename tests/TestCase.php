<?php

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Container $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Container();
        $this->app->instance('events', new Dispatcher($this->app));
        $this->app->singleton('session', function () {
            return new Store('laracart', new ArraySessionHandler(120));
        });

        Facade::setFacadeApplication($this->app);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();

        parent::tearDown();
    }
}
