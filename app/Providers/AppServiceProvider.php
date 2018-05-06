<?php

namespace App\Providers;

use App\Components\File\Contracts\IFileGroupRepository;
use App\Components\File\Contracts\IFileRepository;
use App\Components\File\Repositories\FileGroupRepository;
use App\Components\File\Repositories\FileRepository;
use App\Components\User\Contracts\IGroupRepository;
use App\Components\User\Contracts\IPermissionRepository;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Repositories\GroupRepository;
use App\Components\User\Repositories\PermissionRepository;
use App\Components\User\Repositories\UserRepository;
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
    }
}
