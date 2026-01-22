<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            Route::middleware('web')
                ->group(base_path('routes/guru.php'));
            Route::middleware('web')
                ->group(base_path('routes/wali.php'));
            Route::middleware('web')
                ->group(base_path('routes/siswa.php'));
        });
    }
}
