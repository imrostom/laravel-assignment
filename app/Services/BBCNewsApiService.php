<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BBCNewsApiService
{
    protected string $baseUrl = 'https://newsapi.org/v2/';

    public function searchNews($q = ''): array
    {
        $data = [];

        try {
            $i = 1;

            do {
                $response = Http::get($this->baseUrl . 'everything', [
                    'q' => $q,
                    'sources' => 'bbc-news',
                    'from' => now()->subDay()->format('Y-m-d'), // For testing data query purpose
                    'to' => now()->format('Y-m-d'), // For testing data query purpose
                    'apiKey' => config('services.newsapi.key'),
                    'page' => $i,

                ]);

                if ($response->successful()) {
                    $result = $response->json();

                    // Get documents and metadata
                    $totalResults = $result['totalResults'] ?? 0;
                    $articles = $result['articles'] ?? [];

                    if(count($articles)) {
                        $data = array_merge($data, $articles);
                    }

                    $i++; // Increment page for the next request
                } else {
                    // Log unsuccessful response
                    Log::error('API call failed: ' . $response->body());
                    break; // Exit loop on unsuccessful response
                }

            } while ($totalResults > count($data));

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return $data;
    }

    public function syncWithDatabase(): array
    {
        $news = $this->searchNews('');
        dd($news);
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
