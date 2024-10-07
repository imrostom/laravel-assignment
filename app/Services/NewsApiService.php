<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://newsapi.org/v2/';

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    public function searchNews($q = '')
    {
        if (blank($q)) {
            return $this->fetchTopHeadlines();
        }

        $response = Http::get($this->baseUrl . 'everything', [
            'q' => $q,
            'from' => now()->subDays(7)->format('Y-m-d'),
            'apiKey' => $this->apiKey
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    public function fetchTopHeadlines($country = 'us')
    {
        $response = Http::get($this->baseUrl . 'top-headlines', [
            'country' => $country,
            'apiKey' => $this->apiKey,
            'pageSize' => 100
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function syncWithDatabase()
    {
        $news = $this->searchNews();
        if (blank($news)) {
            return [];
        }

        try {
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
