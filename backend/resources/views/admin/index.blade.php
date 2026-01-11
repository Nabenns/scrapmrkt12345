<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRKT Scraper Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gray: {
                            900: '#111827',
                            800: '#1f2937',
                            700: '#374151',
                            600: '#4b5563',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-700">
        <div class="bg-gray-900 p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                MRKT Scraper Control
            </h1>
            <p class="text-gray-400 text-sm mt-1">Manage authentication tokens and scraper status.</p>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 gap-4 p-6 border-b border-gray-700 bg-gray-800/50">
            <div class="bg-gray-700 rounded-lg p-4">
                <h4 class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Total API Hits</h4>
                <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['hits']) }}</p>
            </div>
            <div class="bg-gray-700 rounded-lg p-4">
                <h4 class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Last Access</h4>
                <p class="text-sm font-medium text-white mt-2">{{ $stats['last_access'] }}</p>
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-900/50 border border-green-600 text-green-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900/50 border border-red-600 text-red-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="/admin/tokens" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="token_key" class="block text-sm font-medium text-gray-300 mb-2">Auth0 Key</label>
                    <input type="text" name="token_key" id="token_key" required
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder="@@auth0spajs@@::..."
                    >
                    <p class="text-xs text-gray-500 mt-1">Starts with <code class="bg-gray-800 px-1 rounded">@@auth0spajs@@</code></p>
                </div>

                <div>
                    <label for="token_value" class="block text-sm font-medium text-gray-300 mb-2">Token Value (JSON)</label>
                    <textarea name="token_value" id="token_value" rows="6" required
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        placeholder='{"body": {"access_token": "..."}}'
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Paste the full JSON object value here.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Update Token & Restart Scraper
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-900 px-6 py-4 border-t border-gray-700">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">How to get the token:</h3>
            <ol class="list-decimal list-inside text-sm text-gray-500 space-y-1">
                <li>Go to <a href="https://app.mrktedge.ai" target="_blank" class="text-blue-400 hover:underline">app.mrktedge.ai</a></li>
                <li>Open DevTools (F12) -> Application -> Local Storage</li>
                <li>Copy the <strong>Key</strong> (starts with @@auth0...) to the first field.</li>
                <li>Copy the <strong>Value</strong> (JSON object) to the second field.</li>
            </ol>
        </div>
    </div>

</body>
</html>
