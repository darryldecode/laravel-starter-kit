<?php

namespace App\Providers;

use App\Components\File\Contracts\IFileGroupRepository;
use App\Components\File\Contracts\IFileRepository;
use App\Components\File\Repositories\MySQLFileGroupRepository;
use App\Components\File\Repositories\MySQLFileRepository;
use App\Components\User\Contracts\IGroupRepository;
use App\Components\User\Contracts\IPermissionRepository;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Repositories\MySQLGroupRepository;
use App\Components\User\Repositories\MySQLPermissionRepository;
use App\Components\User\Repositories\MySQLUserRepository;
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
        $this->app->bind(IUserRepository::class, MySQLUserRepository::class);
        $this->app->bind(IGroupRepository::class, MySQLGroupRepository::class);
        $this->app->bind(IPermissionRepository::class, MySQLPermissionRepository::class);
        $this->app->bind(IFileRepository::class, MySQLFileRepository::class);
        $this->app->bind(IFileGroupRepository::class, MySQLFileGroupRepository::class);
    }
}
