<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Currency'                  => 'App\Policies\CurrencyPolicy',
        'App\Models\Client'                    => 'App\Policies\ClientPolicy',
        'App\Models\Provider'                  => 'App\Policies\ProviderPolicy',
        'App\Models\Role'                      => 'App\Policies\RolePolicy',
        'App\Models\Server'                    => 'App\Policies\ServerPolicy',
        'App\Models\Setting'                   => 'App\Policies\SettingPolicy',
        'App\Models\User'                      => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
