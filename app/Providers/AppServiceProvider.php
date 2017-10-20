<?php

namespace App\Providers;

use App\Components\File\Repositories\MySQLFileGroupRepository;
use App\Components\File\Repositories\MySQLFileRepository;
use App\Components\User\Contracts\GroupRepository;
use App\Components\User\Contracts\PermissionRepository;
use App\Components\User\Contracts\UserRepository;
use App\Components\User\Repositories\MySQLGroupRepository;
use App\Components\User\Repositories\MySQLPermissionRepository;
use App\Components\User\Repositories\MySQLUserRepository;
use App\Contracts\FileGroupRepository;
use App\Contracts\FileRepository;
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
        $this->app->bind(PermissionRepository::class, MySQLPermissionRepository::class);
        $this->app->bind(FileRepository::class, MySQLFileRepository::class);
        $this->app->bind(FileGroupRepository::class, MySQLFileGroupRepository::class);
    }
}
