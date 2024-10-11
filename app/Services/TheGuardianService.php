<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheGuardianService
{
    private string $baseUrl = 'https://content.guardianapis.com';

    public function getArticles($query): array
    {
        $data = [];

        try {
            $i = 1;

            do {
                $response = Http::get($this->baseUrl . '/search', [
                    'q' => $query,
                    'from-date' => now()->format('Y-m-d'),
                    'to-date' => now()->format('Y-m-d'),
                    'api-key' => config('services.guardian.key'),
                    'page' => $i,
                    'page-size' => 50,
                ]);

                if ($response->successful()) {
                    $result = $response->json('response');

                    // Get documents and metadata
                    $currentPage = $result['currentPage'] ?? 0;
                    $pages = $result['pages'] ?? 0;
                    $results = $result['results'] ?? [];
                    if (count($results)) {
                        $data = array_merge($data, $results);
                    }

                    $i++; // Increment page for the next request
                } else {
                    // Log unsuccessful response
                    Log::error('API call failed: ' . $response->body());
                    break; // Exit loop on unsuccessful response
                }

            } while ($pages > $currentPage);

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return $data;
    }

    public function syncWithDatabase(): array
    {
        $news = $this->getArticles('');
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
