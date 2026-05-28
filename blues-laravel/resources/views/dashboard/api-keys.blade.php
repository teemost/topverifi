@extends('layouts.dashboard')
@section('title', 'API Access')
@section('page-title', 'API Access')

@section('content')

{{-- Header --}}
<div class="rounded-2xl p-6 mb-6 relative overflow-hidden" style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border:1px solid rgba(249,115,22,0.2)">
    <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-5" style="background:radial-gradient(circle,#f97316 0%,transparent 70%);transform:translate(30%,-30%)"></div>
    <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div class="flex-1">
            <h2 class="text-white font-bold text-lg">Reseller API</h2>
            <p class="text-slate-400 text-sm mt-0.5">Use your API key to access TopVerifi services from your own platform. All requests require the <code class="text-orange-300 bg-slate-800 px-1 rounded text-xs">X-API-Key</code> header.</p>
        </div>
        <a href="#docs" class="shrink-0 inline-flex items-center gap-1.5 border border-slate-600 hover:border-brand text-slate-300 hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            View Docs
        </a>
    </div>
</div>

{{-- Create Key --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 mb-6">
    <h3 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
        <div class="w-1.5 h-4 bg-brand rounded-full"></div>
        Create New API Key
    </h3>
    <form method="POST" action="{{ route('dashboard.api-keys.store') }}" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="text" name="name" placeholder="Key name (e.g. My Platform, Test Key)"
            value="{{ old('name') }}"
            class="flex-1 bg-slate-900 border border-slate-600 text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand transition-colors"
            required maxlength="100">
        <button type="submit"
            class="shrink-0 inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Generate Key
        </button>
    </form>
    <p class="text-slate-500 text-xs mt-3">You can have up to 5 API keys. Keys are shown in full only once — copy it immediately after creation.</p>
</div>

{{-- Keys List --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-2">
        <div class="w-1.5 h-4 bg-brand rounded-full"></div>
        <h3 class="text-white font-semibold text-sm">Your API Keys</h3>
        <span class="ml-auto text-slate-500 text-xs">{{ $keys->count() }} / 5</span>
    </div>

    @forelse($keys as $key)
    <div class="px-5 py-4 border-b border-slate-700/50 last:border-0 {{ !$key->is_active ? 'opacity-60' : '' }}">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-white font-semibold text-sm">{{ $key->name }}</span>
                    @if($key->is_active)
                        <span class="text-xs bg-green-500/10 text-green-400 border border-green-500/20 px-2 py-0.5 rounded-full font-medium">Active</span>
                    @else
                        <span class="text-xs bg-slate-700 text-slate-400 border border-slate-600 px-2 py-0.5 rounded-full font-medium">Disabled</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <code id="key-{{ $key->id }}" class="text-xs font-mono text-slate-400 bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-700 select-all max-w-xs sm:max-w-sm truncate block">
                        {{ substr($key->key, 0, 12) }}••••••••••••••••••••••••••••••••••••••
                    </code>
                    <button onclick="revealKey('{{ $key->id }}', '{{ $key->key }}')"
                        class="text-slate-500 hover:text-brand transition-colors shrink-0" title="Show / Copy">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
                <p class="text-slate-600 text-xs mt-1">
                    Created {{ $key->created_at->diffForHumans() }}
                    @if($key->last_used_at)
                        · Last used {{ $key->last_used_at->diffForHumans() }}
                    @else
                        · Never used
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <form method="POST" action="{{ route('dashboard.api-keys.toggle', $key->id) }}">
                    @csrf
                    <button type="submit"
                        class="text-xs border px-3 py-1.5 rounded-lg font-medium transition-colors
                        {{ $key->is_active ? 'border-slate-600 text-slate-400 hover:border-yellow-500 hover:text-yellow-400' : 'border-slate-600 text-slate-400 hover:border-green-500 hover:text-green-400' }}">
                        {{ $key->is_active ? 'Disable' : 'Enable' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('dashboard.api-keys.destroy', $key->id) }}"
                      onsubmit="return confirm('Revoke this API key? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs border border-slate-600 text-slate-400 hover:border-red-500 hover:text-red-400 px-3 py-1.5 rounded-lg font-medium transition-colors">
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
        <p class="text-slate-500 text-sm">No API keys yet — create one above to get started.</p>
    </div>
    @endforelse
</div>

{{-- Docs --}}
<div id="docs" class="space-y-4">
    <h3 class="text-white font-bold text-base flex items-center gap-2">
        <div class="w-1.5 h-5 bg-brand rounded-full"></div>
        API Reference
    </h3>
    <p class="text-slate-400 text-sm">Base URL: <code class="text-orange-300 bg-slate-800 px-2 py-0.5 rounded text-xs">{{ url('/api/v1') }}</code></p>
    <p class="text-slate-400 text-sm">All requests need the header: <code class="text-orange-300 bg-slate-800 px-2 py-0.5 rounded text-xs">X-API-Key: your_api_key</code></p>

    @foreach([
        [
            'label'    => 'Wallet',
            'color'    => 'green',
            'endpoints' => [
                ['GET', '/balance', 'Get wallet balance', [], '{"balance":12450.00,"currency":"NGN"}'],
            ]
        ],
        [
            'label'    => 'SMM Boosting',
            'color'    => 'orange',
            'endpoints' => [
                ['GET',  '/smm/services',   'List all SMM services with prices', [], '{"services":[{"service_id":1,"name":"Instagram Followers","category":"Instagram","min":10,"max":10000,"rate_per_1k":250}]}'],
                ['POST', '/smm/order',      'Place an SMM order', [['service_id','integer','JAP service ID'],['link','string','Target URL'],['quantity','integer','Amount to deliver']], '{"order_id":42,"status":"pending","charge":500.00,"currency":"NGN"}'],
                ['GET',  '/smm/order/{id}', 'Get order status',   [], '{"order_id":42,"service":"Instagram Followers","status":"in_progress","remains":800,"charge":500.00}'],
            ]
        ],
        [
            'label'    => 'Virtual Numbers',
            'color'    => 'blue',
            'endpoints' => [
                ['GET',    '/numbers/countries',   'List available countries',   [['provider','string','grizzlysms | fivesim | herosms (default: grizzlysms)']], '{"countries":[...]}'],
                ['GET',    '/numbers/services',    'List services for a country',[['provider','string','Provider key'],['country','string','Country code']], '{"services":[...]}'],
                ['POST',   '/numbers/order',       'Order a virtual number',     [['provider','string','Provider'],['service_id','string','Service ID'],['country','string','Country code (optional)'],['price','number','Cost in NGN'],['service_name','string','Label (optional)']], '{"order_id":7,"phone_number":"+12025550199","status":"active","cost":450.00}'],
                ['GET',    '/numbers/{id}/sms',    'Check for incoming SMS',     [], '{"order_id":7,"phone_number":"+12025550199","sms_code":"123456","status":"active"}'],
                ['DELETE', '/numbers/{id}',        'Cancel & refund a number',   [], '{"order_id":7,"status":"cancelled","refunded":true}'],
            ]
        ],
    ] as $section)
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-700 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full
                @if($section['color']==='green') bg-green-400
                @elseif($section['color']==='orange') bg-brand
                @else bg-blue-400
                @endif"></div>
            <span class="text-white font-semibold text-sm">{{ $section['label'] }}</span>
        </div>
        @foreach($section['endpoints'] as [$method, $path, $desc, $params, $response])
        <div class="px-5 py-4 border-b border-slate-700/50 last:border-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <span class="text-xs font-bold px-2 py-0.5 rounded font-mono
                    @if($method==='GET') bg-green-500/10 text-green-400
                    @elseif($method==='POST') bg-blue-500/10 text-blue-400
                    @else bg-red-500/10 text-red-400
                    @endif">{{ $method }}</span>
                <code class="text-orange-300 text-xs font-mono">/api/v1{{ $path }}</code>
                <span class="text-slate-400 text-xs">{{ $desc }}</span>
            </div>
            @if(count($params))
            <div class="mt-2 space-y-1">
                @foreach($params as [$pname, $ptype, $pdesc])
                <div class="flex items-start gap-2 text-xs">
                    <code class="text-slate-300 font-mono bg-slate-900 px-1.5 py-0.5 rounded shrink-0">{{ $pname }}</code>
                    <span class="text-slate-600">{{ $ptype }}</span>
                    <span class="text-slate-500">{{ $pdesc }}</span>
                </div>
                @endforeach
            </div>
            @endif
            <div class="mt-3">
                <p class="text-slate-600 text-xs mb-1 font-semibold uppercase tracking-wider">Response</p>
                <pre class="text-xs text-slate-400 bg-slate-900 rounded-lg px-3 py-2 overflow-x-auto font-mono whitespace-pre-wrap break-all">{{ $response }}</pre>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5">
        <h4 class="text-white font-semibold text-sm mb-3">Error Codes</h4>
        <div class="space-y-2">
            @foreach([[401,'Unauthorized — missing or invalid API key'],[402,'Insufficient wallet balance'],[404,'Resource not found'],[422,'Validation error — check request body'],[500,'Server error — try again or contact support'],[502,'Upstream provider error'],[503,'Service temporarily disabled']] as [$code,$msg])
            <div class="flex items-center gap-3 text-xs">
                <code class="font-mono font-bold text-red-400 w-8 shrink-0">{{ $code }}</code>
                <span class="text-slate-400">{{ $msg }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function revealKey(id, fullKey) {
    const el = document.getElementById('key-' + id);
    if (el.dataset.revealed) {
        navigator.clipboard.writeText(fullKey).then(() => {
            const orig = el.textContent;
            el.textContent = '✓ Copied!';
            setTimeout(() => el.textContent = fullKey, 1800);
        });
        return;
    }
    el.textContent = fullKey;
    el.dataset.revealed = '1';
    navigator.clipboard.writeText(fullKey).then(() => {}).catch(() => {});
}
</script>
@endpush
