<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})
    ->purpose('Display an inspiring quote')
    ->hourly();


Schedule::command('app:sync-bbc-news')
    ->description('sync-bbc-news data with database')
    ->hourly();


Schedule::command('app:sync-guardians')
    ->description('sync-guardians data with database')
    ->hourly();


Schedule::command('app:sync-new-york-times')
    ->description('sync-new-york-times data with database')
    ->hourly();

Schedule::command('app:sync-news-api')
    ->description('sync-news-api data with database')
    ->hourly();
