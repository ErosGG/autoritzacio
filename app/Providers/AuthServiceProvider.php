<?php

namespace App\Providers;

use App\Models\Link;
use App\Models\User;
use App\Policies\LinkPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Link::class => LinkPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define("edit-link", function (User $user, Link $link) {
            $count = DB::table("link_user")
                ->where("user_id", $user->id)
                ->where("link_id", $link->id)
                ->get()->count();
            return $count === 1;
        });
    }
}
