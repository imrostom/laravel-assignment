<?php

use App\Services\BBCNewsApiService;
use App\Services\NewsApiService;
use App\Services\NewYorkTimesService;
use App\Services\TheGuardianService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('newsapi', function () {
   dd(app(NewsApiService::class)->syncWithDatabase());
});

Route::get('bbc', function () {
    dd(app(BBCNewsApiService::class)->syncWithDatabase());
});


Route::get('nytime', function () {
    dd(app(NewYorkTimesService::class)->syncWithDatabase());
});


Route::get('guardianapis', function () {
    dd(app(TheGuardianService::class)->syncWithDatabase());
});