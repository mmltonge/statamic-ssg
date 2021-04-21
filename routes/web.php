<?php

use Illuminate\Support\Facades\Route;
use Statamic\Entries\Collection;
use Statamic\Entries\Entry;
use Statamic\StaticSite\Generator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/cp/ssg/generate/{collection?}/{entry?}', function(Collection $collection, Entry $entry) {
    $response = (new \App\Generator(app(), app()['files'], $collection, $entry))
        ->generate();

    // Would be nice to have the output to CP, not sure how to
    dump($response);
    return '<a href="' . route('statamic.cp.dashboard') . '">Return to Dashboard</a>';
})->name('statamic.cp.ssg.generate');
