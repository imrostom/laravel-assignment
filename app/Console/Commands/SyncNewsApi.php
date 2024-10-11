<?php

namespace App\Console\Commands;

use App\Services\NewsApiService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncNewsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-news-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data into database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            app(NewsApiService::class)->syncWithDatabase();
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
