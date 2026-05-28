@extends('layouts.dashboard')
@section('title', 'API Access')
@section('page-title', 'API Access')

@section('content')

{{-- Hero --}}
<div class="rounded-2xl p-6 mb-6 relative overflow-hidden" style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border:1px solid rgba(249,115,22,0.2)">
    <div class="absolute top-0 right-0 w-56 h-56 rounded-full opacity-5" style="background:radial-gradient(circle,#f97316 0%,transparent 70%);transform:translate(30%,-30%)"></div>
    <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-white font-bold text-lg">Reseller API</h2>
            <p class="text-slate-400 text-sm mt-0.5">Integrate TopVerifi into your own platform. Authenticate with <code class="text-orange-300 bg-slate-800/80 px-1.5 py-0.5 rounded text-xs font-mono">X-API-Key: &lt;your_key&gt;</code> on every request.</p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('dashboard.api-guide') }}"
               class="inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Integration Guide
            </a>
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @foreach([
        [$totalCalls,   'Total Calls',    'text-white',       'bg-slate-700/50'],
        [$successCalls, 'Successful',     'text-green-400',   'bg-green-500/10'],
        [$errorCalls,   'Errors',         'text-red-400',     'bg-red-500/10'],
        [$avgMs.'ms',   'Avg Response',   'text-brand',       'bg-orange-500/10'],
    ] as [$val, $lbl, $tc, $bg])
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4">
        <p class="font-bold text-xl {{ $tc }}">{{ $val }}</p>
        <p class="text-slate-500 text-xs mt-0.5">{{ $lbl }}</p>
    </div>
    @endforeach
</div>

{{-- Tab nav --}}
<div class="flex gap-1 mb-5 bg-slate-800 border border-slate-700 rounded-xl p-1 w-fit">
    <button onclick="showTab('keys')" id="btn-keys"
        class="tab-nav-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors bg-brand text-white">
        API Keys
    </button>
    <button onclick="showTab('logs')" id="btn-logs"
        class="tab-nav-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors text-slate-400 hover:text-white">
        Request Logs
    </button>
    <button onclick="showTab('docs')" id="btn-docs"
        class="tab-nav-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors text-slate-400 hover:text-white">
        Quick Reference
    </button>
</div>

{{-- ── TAB: Keys ─────────────────────────────────────────────────────────── --}}
<div id="tab-keys">
    {{-- Create --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 mb-5">
        <h3 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
            <div class="w-1.5 h-4 bg-brand rounded-full"></div>
            Create New API Key
        </h3>
        <form method="POST" action="{{ route('dashboard.api-keys.store') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="name" placeholder="e.g. My SMM Panel, Production Key"
                value="{{ old('name') }}"
                class="flex-1 bg-slate-900 border border-slate-600 focus:border-brand text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm outline-none transition-colors"
                required maxlength="100" autocomplete="off">
            <button type="submit"
                class="shrink-0 inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Generate Key
            </button>
        </form>
        <p class="text-slate-600 text-xs mt-3">Max 5 keys per account. Copy your key immediately — it will be masked after you leave this page.</p>
    </div>

    {{-- Keys list --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-2">
            <div class="w-1.5 h-4 bg-brand rounded-full"></div>
            <h3 class="text-white font-semibold text-sm">Your Keys</h3>
            <span class="ml-auto text-slate-500 text-xs">{{ $keys->count() }} / 5</span>
        </div>

        @forelse($keys as $key)
        <div class="px-5 py-4 border-b border-slate-700/50 last:border-0 {{ !$key->is_active ? 'opacity-60' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-white font-semibold text-sm">{{ $key->name }}</span>
                        @if($key->is_active)
                            <span class="text-[10px] bg-green-500/10 text-green-400 border border-green-500/20 px-2 py-0.5 rounded-full font-semibold">Active</span>
                        @else
                            <span class="text-[10px] bg-slate-700 text-slate-500 border border-slate-600 px-2 py-0.5 rounded-full font-semibold">Disabled</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <code id="key-display-{{ $key->id }}"
                            class="text-xs font-mono text-slate-400 bg-slate-900 border border-slate-700 px-3 py-1.5 rounded-lg select-all max-w-sm truncate block">{{ substr($key->key, 0, 10) }}••••••••••••••••••••••••••••••••••••••••••</code>
                        <button onclick="toggleKeyReveal({{ $key->id }}, '{{ $key->key }}')"
                            title="Reveal &amp; Copy"
                            class="text-slate-500 hover:text-brand transition-colors shrink-0 p-1">
                            <svg id="eye-{{ $key->id }}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p class="text-slate-600 text-xs mt-1.5 flex items-center gap-3">
                        <span>Created {{ $key->created_at->diffForHumans() }}</span>
                        @if($key->last_used_at)
                            <span class="text-green-600">· Last used {{ $key->last_used_at->diffForHumans() }}</span>
                        @else
                            <span>· Never used</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('dashboard.api-keys.toggle', $key->id) }}">
                        @csrf
                        <button type="submit" class="text-xs border px-3 py-1.5 rounded-lg font-medium transition-colors
                            {{ $key->is_active
                                ? 'border-slate-600 text-slate-400 hover:border-yellow-500/60 hover:text-yellow-400'
                                : 'border-slate-600 text-slate-400 hover:border-green-500/60 hover:text-green-400' }}">
                            {{ $key->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('dashboard.api-keys.destroy', $key->id) }}"
                          onsubmit="return confirm('Permanently revoke this API key? Any integration using it will stop working immediately.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs border border-slate-600 text-slate-400 hover:border-red-500/60 hover:text-red-400 px-3 py-1.5 rounded-lg font-medium transition-colors">
                            Revoke
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-12 text-center">
            <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <p class="text-slate-500 text-sm mb-1">No API keys yet</p>
            <p class="text-slate-600 text-xs">Create your first key above to start integrating.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ── TAB: Logs ─────────────────────────────────────────────────────────── --}}
<div id="tab-logs" class="hidden">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-4 bg-brand rounded-full"></div>
                <h3 class="text-white font-semibold text-sm">Request Logs</h3>
            </div>
            <span class="text-slate-500 text-xs">Last 100 requests</span>
        </div>

        @if($logs->isEmpty())
        <div class="px-5 py-12 text-center">
            <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="text-slate-500 text-sm">No API calls yet. Start making requests with your key.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-700 text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left font-semibold">Time</th>
                        <th class="px-5 py-3 text-left font-semibold">Method</th>
                        <th class="px-5 py-3 text-left font-semibold">Endpoint</th>
                        <th class="px-5 py-3 text-left font-semibold">Status</th>
                        <th class="px-5 py-3 text-left font-semibold">Key</th>
                        <th class="px-5 py-3 text-left font-semibold">Response</th>
                        <th class="px-5 py-3 text-left font-semibold">IP</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                <tr class="border-b border-slate-700/40 last:border-0 hover:bg-slate-700/20 transition-colors">
                    <td class="px-5 py-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('M d, H:i:s') }}</td>
                    <td class="px-5 py-3">
                        <span class="font-mono font-bold
                            {{ $log->method === 'GET'    ? 'text-green-400' :
                               ($log->method === 'POST'   ? 'text-blue-400'  :
                               ($log->method === 'DELETE' ? 'text-red-400'   : 'text-slate-400')) }}">
                            {{ $log->method }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-mono text-orange-300 max-w-[200px] truncate">{{ $log->path }}</td>
                    <td class="px-5 py-3">
                        <span class="font-bold font-mono
                            {{ $log->status_code < 300 ? 'text-green-400' :
                               ($log->status_code < 500 ? 'text-yellow-400' : 'text-red-400') }}">
                            {{ $log->status_code }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $log->apiKey?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-400 whitespace-nowrap">
                        @if($log->response_ms !== null)
                            <span class="{{ $log->response_ms < 500 ? 'text-green-400' : ($log->response_ms < 2000 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $log->response_ms }}ms
                            </span>
                        @else —
                        @endif
                    </td>
                    <td class="px-5 py-3 text-slate-600 font-mono">{{ $log->ip }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ── TAB: Quick Reference ────────────────────────────────────────────────── --}}
<div id="tab-docs" class="hidden space-y-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5">
        <p class="text-slate-300 text-sm mb-1 font-semibold">Base URL</p>
        <code class="text-orange-300 text-sm font-mono">{{ url('/api/v1') }}</code>
        <p class="text-slate-400 text-sm mt-4 mb-1 font-semibold">Authentication header</p>
        <code class="text-orange-300 text-sm font-mono">X-API-Key: your_api_key_here</code>
        <p class="text-slate-500 text-xs mt-4">All responses are JSON. See the <a href="{{ route('dashboard.api-guide') }}" class="text-brand hover:underline">full integration guide</a> for code examples in PHP, Python, and JavaScript.</p>
    </div>

    @foreach([
        ['Wallet',          'green',  [['GET','/balance','Get wallet balance and currency']]],
        ['SMM Boosting',    'orange', [
            ['GET', '/smm/services',   'List all services with pricing'],
            ['POST','/smm/order',      'Place SMM order (service_id, link, quantity)'],
            ['GET', '/smm/order/{id}', 'Get order status & progress'],
        ]],
        ['Virtual Numbers', 'blue',   [
            ['GET',   '/numbers/countries',  'List countries (?provider=grizzlysms|fivesim|herosms)'],
            ['GET',   '/numbers/services',   'List services (?country=XX&provider=XX)'],
            ['POST',  '/numbers/order',      'Order number (provider, service_id, country, price)'],
            ['GET',   '/numbers/{id}/sms',   'Poll for SMS code'],
            ['DELETE','/numbers/{id}',       'Cancel & auto-refund'],
        ]],
    ] as [$section, $color, $endpoints])
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-700 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full
                @if($color==='green') bg-green-400
                @elseif($color==='orange') bg-brand
                @else bg-blue-400 @endif"></div>
            <span class="text-white font-semibold text-sm">{{ $section }}</span>
        </div>
        @foreach($endpoints as [$method, $path, $desc])
        <div class="flex flex-wrap items-center gap-3 px-5 py-3 border-b border-slate-700/40 last:border-0 text-xs">
            <span class="font-mono font-bold shrink-0
                {{ $method==='GET' ? 'text-green-400' : ($method==='POST' ? 'text-blue-400' : 'text-red-400') }}">{{ $method }}</span>
            <code class="text-orange-300 font-mono shrink-0">/api/v1{{ $path }}</code>
            <span class="text-slate-500">{{ $desc }}</span>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5">
        <p class="text-white font-semibold text-sm mb-3">HTTP Status Codes</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach([[200,'OK — request succeeded'],[201,'Created — order placed'],[401,'Unauthorized — bad/missing key'],[402,'Payment required — low balance'],[404,'Not found'],[422,'Validation error'],[429,'Rate limit exceeded (60 req/min)'],[500,'Server error'],[502,'Upstream provider error'],[503,'Service disabled by admin']
            ] as [$code, $msg])
            <div class="flex items-center gap-3 text-xs">
                <code class="font-mono font-bold w-8 shrink-0
                    {{ $code < 300 ? 'text-green-400' : ($code < 500 ? 'text-yellow-400' : 'text-red-400') }}">{{ $code }}</code>
                <span class="text-slate-400">{{ $msg }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const revealed = {};

function toggleKeyReveal(id, fullKey) {
    const el  = document.getElementById('key-display-' + id);
    const eye = document.getElementById('eye-' + id);
    if (revealed[id]) {
        navigator.clipboard.writeText(fullKey).then(() => {
            const orig = el.textContent;
            el.textContent = '✓ Copied to clipboard!';
            setTimeout(() => el.textContent = fullKey, 1800);
        }).catch(() => {});
        return;
    }
    revealed[id] = true;
    el.textContent = fullKey;
    eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    navigator.clipboard.writeText(fullKey).catch(() => {});
}

function showTab(tab) {
    ['keys','logs','docs'].forEach(t => {
        document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('btn-' + t);
        if (t === tab) {
            btn.className = 'tab-nav-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors bg-brand text-white';
        } else {
            btn.className = 'tab-nav-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors text-slate-400 hover:text-white';
        }
    });
}

// Auto-open logs tab if URL has #logs
if (window.location.hash === '#logs') showTab('logs');
</script>
@endpush
