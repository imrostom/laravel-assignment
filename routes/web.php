<?php

use App\Services\NewsApiService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('newsapi', function () {
   dd(app(NewsApiService::class)->searchNews());
});