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
