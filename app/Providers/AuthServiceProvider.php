<?php

namespace App\Providers;
use App\Models\Article;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\UserPolicy;
use App\Models\Client;
use App\Models\Dette;
use App\Models\ModelFirebase;
use App\Policies\ClientPolicy;
use App\Policies\DetteArchivePolicy;
use App\Policies\DettePolicy;
use App\Policies\NotifClientPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Dette::class => DettePolicy::class,
        ModelFirebase::class => DetteArchivePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }

}
