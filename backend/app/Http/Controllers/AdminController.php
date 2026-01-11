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
        ];

        try {
            $stats['hits'] = \Illuminate\Support\Facades\Redis::get('api:stats:hits') ?? 0;
            $stats['last_access'] = \Illuminate\Support\Facades\Redis::get('api:stats:last_access') ?? 'Never';
        } catch (\Exception $e) {
            // Ignore
        }

        return view('admin.index', compact('stats'));
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'token_key' => 'required|string',
            'token_value' => 'required|json',
        ]);

        try {
            $key = $request->input('token_key');
            $value = json_decode($request->input('token_value'), true);

            // Construct the full JSON structure expected by the scraper
            $data = [
                $key => $value
            ];

            // Save to the shared volume
            // The volume is mounted at storage/app/scraper_tokens
            // So we use the 'local' disk but point to that directory or just use absolute path if needed.
            // Since we mounted it to storage/app/scraper_tokens, we can use the 'local' disk if we configure it,
            // or just use file_put_contents on the absolute path.
            
            $path = storage_path('app/scraper_tokens/local_storage.json');
            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

            // Create a trigger file to notify the scraper to run immediately
            $triggerPath = storage_path('app/scraper_tokens/trigger.txt');
            file_put_contents($triggerPath, 'run');

            return back()->with('success', 'Token updated successfully! Scraper triggered to run immediately.');
        } catch (\Exception $e) {
            Log::error('Failed to update token: ' . $e->getMessage());
            return back()->with('error', 'Failed to update token: ' . $e->getMessage());
        }
    }
}
