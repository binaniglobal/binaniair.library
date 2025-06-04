<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);
        app(PermissionRegistrar::class)->setPermissionClass(Permission::class);
        app(PermissionRegistrar::class)->setRoleClass(Role::class);
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
//                Model::automaticallyEagerLoadRelationships();
//        Model::preventLazyLoading(!app()->environment('production'));
    }
}
