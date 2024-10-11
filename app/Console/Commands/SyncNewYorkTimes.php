<?php

namespace App\Console\Commands;

use App\Services\NewYorkTimesService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncNewYorkTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-new-york-times';

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
            app(NewYorkTimesService::class)->syncWithDatabase();
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
