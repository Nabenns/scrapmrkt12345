<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'hits' => 0,
            'last_access' => 'Never',
            'total_requests' => 0,
        ];

        try {
            $stats['hits'] = \Illuminate\Support\Facades\Redis::get('api:stats:hits') ?? 0;
            $stats['last_access'] = \Illuminate\Support\Facades\Redis::get('api:stats:last_access') ?? 'Never';
            $stats['total_requests'] = \Illuminate\Support\Facades\Redis::get('api:analytics:total_requests') ?? 0;
        } catch (\Exception $e) {
            // Redis not available, ignore
            \Illuminate\Support\Facades\Log::warning('Redis not available for admin stats: ' . $e->getMessage());
        } catch (\Error $e) {
             // Handle Class not found error
             \Illuminate\Support\Facades\Log::warning('Redis class not found: ' . $e->getMessage());
        }

        return view('admin.index', compact('stats'));
    }

    public function updateToken(Request $request)
    {
        // In a real scenario, we might parse an uploaded file or a pasted JSON.
        // For now, we'll just touch the trigger file to restart the scraper if the user clicks "Update".
        // But the UI form is actually just a button to run the scraper in the new design.
        // Let's keep this for backward compatibility or future token pasting.
        
        return back()->with('error', 'Token update via UI is not fully implemented yet. Please use the "Run Scraper" button.');
    }

    public function triggerScraper()
    {
        try {
            // Create a trigger file to notify the scraper to run immediately
            $triggerPath = storage_path('app/scraper_tokens/trigger.txt');
            file_put_contents($triggerPath, 'run');

            return back()->with('success', 'Scraper triggered successfully! Check logs for progress.');
        } catch (\Exception $e) {
            Log::error('Failed to trigger scraper: ' . $e->getMessage());
            return back()->with('error', 'Failed to trigger scraper: ' . $e->getMessage());
        }
    }
}
