<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\ClubRequest;
use App\Policies\ClubRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ClubRequest::class => ClubRequestPolicy::class,
        Club::class => ClubPolicy::class,
        Role::class => RolePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Optional: Define additional gates here if needed
    }
}
