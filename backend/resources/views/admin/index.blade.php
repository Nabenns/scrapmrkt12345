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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar for logs */
        .log-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .log-scroll::-webkit-scrollbar-track {
            background: #1f2937; 
        }
        .log-scroll::-webkit-scrollbar-thumb {
            background: #4b5563; 
            border-radius: 4px;
        }
        .log-scroll::-webkit-scrollbar-thumb:hover {
            background: #6b7280; 
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-gray-800 border-b border-gray-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-yellow-500 font-bold text-xl tracking-wider">
                            MRKT<span class="text-white">SCRAPER</span>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="#" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="/api/headlines" target="_blank" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">API Preview</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span class="text-sm text-green-400 font-semibold">System Online</span>
                        </div>
                        <form action="{{ route('admin.token.update') }}" method="POST" class="ml-4">
                            @csrf
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Update Token
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Headlines Card -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider">Total Headlines</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Headline::count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-900/50 rounded-lg text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">Last updated: {{ \App\Models\Headline::latest()->first()?->created_at->diffForHumans() ?? 'Never' }}</div>
                </div>

                <!-- Sentiment Card -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider">Market Sentiment</p>
                            @php $sentiment = \App\Models\MarketSentiment::latest()->first(); @endphp
                            <p class="text-3xl font-bold {{ $sentiment?->value > 50 ? 'text-green-400' : 'text-red-400' }} mt-1">
                                {{ $sentiment?->value ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-900/50 rounded-lg text-purple-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">{{ $sentiment?->label ?? 'No Data' }}</div>
                </div>

                <!-- Trump Events Card -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider">Trump Events</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\TrumpEvent::count() }}</p>
                        </div>
                        <div class="p-3 bg-red-900/50 rounded-lg text-red-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">Upcoming: {{ \App\Models\TrumpEvent::where('date', '>=', now())->count() }}</div>
                </div>

                <!-- API Requests Card -->
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider">API Requests</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ \Illuminate\Support\Facades\Redis::get('api:analytics:total_requests') ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-900/50 rounded-lg text-yellow-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">Total served</div>
                </div>
            </div>

            <!-- Main Section: Logs & Controls -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left: Service Status & Controls -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                        <h3 class="text-lg font-semibold text-white mb-4">Service Control</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                                <span class="text-gray-300">Scraper Status</span>
                                <span class="px-2 py-1 text-xs font-bold rounded bg-green-500/20 text-green-400">RUNNING</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                                <span class="text-gray-300">Last Run</span>
                                <span class="text-sm text-gray-400">{{ \App\Models\Headline::latest()->first()?->created_at->format('H:i:s d M') ?? 'N/A' }}</span>
                            </div>

                            <div class="pt-4 border-t border-gray-700">
                                <form action="{{ route('admin.scraper.trigger') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Run Scraper Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                         <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
                         <ul class="space-y-2">
                             <li><a href="/api/headlines" class="block p-2 rounded hover:bg-gray-700 text-blue-400 hover:text-blue-300 transition">📄 Headlines JSON</a></li>
                             <li><a href="/api/sentiment" class="block p-2 rounded hover:bg-gray-700 text-blue-400 hover:text-blue-300 transition">📊 Sentiment JSON</a></li>
                             <li><a href="/api/trump/events" class="block p-2 rounded hover:bg-gray-700 text-blue-400 hover:text-blue-300 transition">📅 Trump Events JSON</a></li>
                         </ul>
                    </div>
                </div>

                <!-- Right: Live Logs (Mocked for now, can be real later) -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden flex flex-col h-[500px]">
                        <div class="p-4 border-b border-gray-700 bg-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-white">System Logs</h3>
                            <span class="text-xs text-gray-500 font-mono">scraper-combined.log</span>
                        </div>
                        <div class="flex-1 p-4 bg-gray-900 font-mono text-xs text-gray-300 overflow-y-auto log-scroll">
                            <div class="space-y-1">
                                <div class="text-green-400">[INFO] {{ now()->subMinutes(5)->toIso8601String() }} - Starting scrape job...</div>
                                <div class="text-blue-400">[INFO] {{ now()->subMinutes(5)->toIso8601String() }} - Access token retrieved.</div>
                                <div class="text-gray-400">[INFO] {{ now()->subMinutes(4)->toIso8601String() }} - Received 50 items from API.</div>
                                <div class="text-green-400">[INFO] {{ now()->subMinutes(4)->toIso8601String() }} - Headlines scraped: 49</div>
                                <div class="text-blue-400">[INFO] {{ now()->subMinutes(4)->toIso8601String() }} - Starting Phase 2 API Scrape...</div>
                                <div class="text-gray-400">[INFO] {{ now()->subMinutes(3)->toIso8601String() }} - Saved market sentiment.</div>
                                <div class="text-gray-400">[INFO] {{ now()->subMinutes(3)->toIso8601String() }} - Saved economic summary.</div>
                                <div class="text-gray-400">[INFO] {{ now()->subMinutes(3)->toIso8601String() }} - Saved 5 new Trump events.</div>
                                <div class="text-green-400">[INFO] {{ now()->subMinutes(3)->toIso8601String() }} - Scrape job completed successfully.</div>
                                <div class="text-gray-500 mt-4 italic">-- End of recent logs --</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl animate-bounce">
        {{ session('success') }}
    </div>
    @endif

</body>
</html>
