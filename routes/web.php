<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
});

Route::get('newsapi', [TestController::class, 'syncNewsApi']);
Route::get('bbc', [TestController::class, 'syncBBCNews']);
Route::get('nytime', [TestController::class, 'syncNYTimes']);
Route::get('guardian', [TestController::class, 'syncTheGuardian']);
