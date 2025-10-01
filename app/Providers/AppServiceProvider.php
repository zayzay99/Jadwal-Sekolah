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
            // Logic to ensure an active academic year is always set
            $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();

            // If no year is active, find the latest one and activate it.
            if (!$activeTahunAjaran) {
                $latestTahunAjaran = TahunAjaran::orderBy('tahun_ajaran', 'desc')->first();
                if ($latestTahunAjaran) {
                    TahunAjaran::where('is_active', true)->update(['is_active' => false]); // Deactivate others first
                    $latestTahunAjaran->update(['is_active' => true]);
                    $activeTahunAjaran = $latestTahunAjaran;
                }
            }

            // Ensure the session is always in sync with the active year
            session(['tahun_ajaran_id' => $activeTahunAjaran?->id]);

            $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
            $view->with(compact('activeTahunAjaran', 'tahunAjarans'));
        });
    }
}