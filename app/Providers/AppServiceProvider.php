<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TahunAjaran;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('dashboard.*', function ($view) {
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
            $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
            $view->with(compact('activeTahunAjaran', 'tahunAjarans'));
        });
    }
}