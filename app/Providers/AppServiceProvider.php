<?php

namespace App\Providers;

use App\Contracts\GroupRepository;
use App\Contracts\PermissionRepository;
use App\Contracts\UserRepository;
use App\Repositories\MySQLGroupRepository;
use App\Repositories\MySQLPermissionRepositoryRepository;
use App\Repositories\MySQLUserRepository;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        // bindings
        $this->app->bind(UserRepository::class, MySQLUserRepository::class);
        $this->app->bind(GroupRepository::class, MySQLGroupRepository::class);
        $this->app->bind(PermissionRepository::class, MySQLPermissionRepositoryRepository::class);
    }
}
