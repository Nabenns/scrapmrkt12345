<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'token_json' => 'required|json',
        ]);

        $json = $request->input('token_json');

        // Verify it has the access token structure we need
        $data = json_decode($json, true);
        $hasToken = false;
        
        foreach ($data as $key => $value) {
            if (str_contains($key, '::openid profile email offline_access') && isset($value['body']['access_token'])) {
                $hasToken = true;
                break;
            }
        }

        if (!$hasToken) {
            return back()->withErrors(['token_json' => 'Invalid JSON: Could not find access_token in the provided data. Make sure you copied the full Local Storage value.']);
        }

        // Save to the shared volume
        // Path matches the volume mount in docker-compose: /var/www/html/storage/app/scraper_tokens
        $path = storage_path('app/scraper_tokens/local_storage.json');
        file_put_contents($path, $json);

        return back()->with('success', 'Token updated successfully! The scraper will use the new token on its next run.');
    }
}
