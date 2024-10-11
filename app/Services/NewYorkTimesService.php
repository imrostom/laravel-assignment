<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesService
{
    private string $baseUrl = 'https://api.nytimes.com/svc/search/v2';

    public function getArticles($query): array
    {
        $data = [];

        try {
            $i = 0;

            do {
                $response = Http::get($this->baseUrl . '/articlesearch.json', [
                    'q' => $query,
                    'begin_date' => now()->format('Ymd'),
                    'end_date' => now()->format('Ymd'),
                    'api-key' => config('services.nytimes.key'),
                    'page' => $i,
                ]);

                if ($response->successful()) {
                    $result = $response->json('response');

                    // Get documents and metadata
                    $docs = $result['docs'] ?? [];
                    $hits = $result['meta']['hits'] ?? 0;
                    $offset = $result['meta']['offset'] ?? 0;

                    if(count($docs)) {
                        $data = array_merge($data, $docs);
                    }

                    $i++; // Increment page for the next request
                } else {
                    // Log unsuccessful response
                    Log::error('API call failed: ' . $response->body());
                    break; // Exit loop on unsuccessful response
                }

            } while ($hits > $offset);

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return $data;
    }

    public function syncWithDatabase(): array
    {
        $news = $this->getArticles('war');
        if (blank($news)) {
            return [];
        }

        try {
            // Here ignore batch insert & pagination
            foreach ($news as $article) {
                News::updateOrCreate(
                    ['url' => $article['url']], // Use the URL as a unique identifier
                    [
                        'title' => $article['title'],
                        'description' => $article['description'],
                        'content' => $article['content'],
                        'published_at' => $article['publishedAt'],
                        'source' => $article['source']['name']
                    ]
                );
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return [];
    }
}
