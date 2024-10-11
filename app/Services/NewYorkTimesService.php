<?php

namespace App\Services;

use App\Models\News;
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
            // Here ignore batch insert
            foreach ($news as $article) {
                $imageUrl = null;
                if(isset($article['multimedia'][0])) {
                    $imageUrl = 'https://www.nytimes.com/' . $article['multimedia'][0]['url'];
                }

                News::query()->updateOrCreate(
                    ['url' => $article['web_url']], // Use the URL as a unique identifier
                    [
                        'title' => $article['abstract'],
                        'content' => $article['lead_paragraph'],
                        'image' => $imageUrl,
                        'published_at' => now()->parse($article['pub_date'])->format('Y-m-d H:i:s'),
                        'source' => $article['source'] ?? '',
                        'platform' => 'new-york-times',
                    ]
                );
            }
        } catch (Exception $exception) {
            Log::info($exception);
        }

        return [];
    }
}
