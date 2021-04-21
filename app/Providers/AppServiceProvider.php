<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(config('statamic.ssg_button')) {
            Nav::extend(function ($nav) {
                $nav->content('SSG Site')
                    ->route('ssg.generate');

                $route = Route::currentRouteName();
                if(str_contains($route, 'cp.collections.show') || str_contains($route, 'cp.collections.entries')) {
                    $collection = Route::current()->parameter('collection');
                    $nav->content('SSG Collection')
                        ->route('ssg.generate', ['collection' => $collection->id()]);

                    if(str_contains($route, 'cp.collections.entries.edit')) {
                        $entry = Route::current()->parameter('entry');
                        $nav->content('SSG Entry')
                            ->route('ssg.generate', [
                                'collection' => $collection->id(),
                                'entry' => $entry->id()
                            ]);
                    }
                }
            });
        }


        // Statamic::script('app', 'cp');
        // Statamic::style('app', 'cp');
    }
}
