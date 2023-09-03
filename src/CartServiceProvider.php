<?php

namespace Miladev\Laracart;

use Illuminate\Support\ServiceProvider;
use Miladev\Laracart\Repository\CartRepository;
use Miladev\Laracart\Repository\CollectionRepository;
use Miladev\Laracart\Repository\DBRepository;

class CartServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');


        $cartStorage = config('cart.storage') ?? 'db';

        $instance = match ($cartStorage) {
            "db" => app()->singleton(CartRepository::class, DBRepository::class),
            "collection" => app()->singleton(CartRepository::class, CollectionRepository::class),
            default => app()->singleton(CartRepository::class, DBRepository::class),
        };

        app()->singleton(CartRepository::class,$instance);

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/config/cart.php' => config_path('cart.php'),
            ], 'config');

        }
    }

    public function register()
    {
        $this->app->bind('cart', function () {
            return new Cart;
        });

        $this->mergeConfigFrom(__DIR__ . '/config/cart.php', 'cart');
    }
}
