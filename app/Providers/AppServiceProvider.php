<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        View::composer(['layouts.navigation', 'welcome'], function (ViewInstance $view): void {
            $view->with(
                'navigationCategories',
                Category::query()->orderBy('sort_order')->orderBy('id')->get(['name', 'slug'])
            );
        });
    }
}
