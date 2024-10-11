<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends BaseApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = News::query();

        // Add platform condition if platform is not empty
        if (!empty($request->get('platform'))) {
            $query->where('platform', $request->get('platform'));
        }

        // Add source condition if source is not empty
        if (!empty($request->get('source'))) {
            $query->where('source', $request->get('source'));
        }

        // Add title condition if title is not empty
        if (!empty($request->get('search'))) {
            $query->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', "%{$request->get('search')}%");
                $query->orWhere('content', 'like', "%{$request->get('search')}%");
            });
        }

        return $this->success([
            'news' => $query->paginate(20),
        ]);
    }

    public function overview()
    {
        $news = News::query()
            ->select('platform', DB::raw('count(*) as total'))
            ->groupBy('platform')
            ->pluck('total', 'platform')
            ->toArray();

        return $this->success([
            'news' => $news,
        ]);
    }

    public function sources()
    {
        $news = News::query()
            ->distinct()
            ->pluck('source')
            ->toArray();

        return $this->success([
            'news' => $news,
        ]);
    }
}
