<?php

namespace App\Http\Controllers;

use App\Services\BBCNewsApiService;
use App\Services\NewsApiService;
use App\Services\NewYorkTimesService;
use App\Services\TheGuardianService;
use Exception;

class TestController extends Controller
{
    /**
     * Sync news data from News API and return a response.
     */
    public function syncNewsApi(NewsApiService $service)
    {
        return $this->syncService($service);
    }

    /**
     * Sync news data from BBC API and return a response.
     */
    public function syncBBCNews(BBCNewsApiService $service)
    {
        return $this->syncService($service);
    }

    /**
     * Sync news data from New York Times API and return a response.
     */
    public function syncNYTimes(NewYorkTimesService $service)
    {
        return $this->syncService($service);
    }

    /**
     * Sync news data from The Guardian API and return a response.
     */
    public function syncTheGuardian(TheGuardianService $service)
    {
        return $this->syncService($service);
    }

    /**
     * A reusable method to sync a service and handle errors.
     */
    protected function syncService($service)
    {
        try {
            return $service->syncWithDatabase();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}