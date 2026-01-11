<?php

namespace App\Http\Controllers;

use App\Models\Headline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HeadlineController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $category = $request->input('category');
        $page = $request->input('page', 1);

        // Increment API Hit Counter
        try {
            \Illuminate\Support\Facades\Redis::incr('api:stats:hits');
            \Illuminate\Support\Facades\Redis::set('api:stats:last_access', now()->toDateTimeString());
        } catch (\Exception $e) {
            // Ignore Redis errors for stats to avoid breaking the API
        }

        $cacheKey = "headlines_v1_{$limit}_{$category}_{$page}";

        $headlines = Cache::remember($cacheKey, 60, function () use ($limit, $category) {
            $query = Headline::query()
                ->orderBy('published_at', 'desc');

            if ($category) {
                $query->where('category', $category);
            }

            return $query->paginate($limit);
        });

        return response()->json($headlines);
    }
}
