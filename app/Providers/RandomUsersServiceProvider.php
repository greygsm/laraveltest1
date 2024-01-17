<?php

namespace App\Providers;

use App\Services\ClientInterface;
use App\Services\RandomUserClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RandomUsersServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Registration services
     */
    public function register(): void
    {
        $this->app->singleton(ClientInterface::class, function () {
            return new RandomUserClient();
        });
    }

    /**
     * Return service provided
     */
    public function provides(): array
    {
        return [ClientInterface::class];
    }
}
