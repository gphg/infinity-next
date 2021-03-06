<?php

namespace App\Providers;

use App\Contracts\PermissionUser;
use App\Services\AuthManager;
use App\Support\Anonymous;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Post::class => \App\Policies\PostPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerAuthenticator();
        $this->registerPolicies($gate);
    }

    /**
     * Register the authenticator services.
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('auth', function ($app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });

        $this->app->singleton('auth.driver', function ($app) {
            return $app['auth']->guard();
        });

        $this->app->singleton(
            PermissionUser::class,
            function ($app) {
                $auth = $app->make('auth');

                if ($auth->guest()) {
                    return new Anonymous;
                } else {
                    return $auth->user();
                }
            }
        );
    }
}
