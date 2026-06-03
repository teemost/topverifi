@extends('layouts.dashboard')
@section('title', 'Virtual Numbers — Server 2')
@section('page-title', 'Virtual Numbers — Server 2')
@section('content')

@if(!$enabled)
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-16 h-16 rounded-2xl bg-slate-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
    </div>
    <h2 class="text-xl font-semibold text-white mb-2">Virtual Numbers Unavailable</h2>
    <p class="text-slate-400 max-w-sm">This feature is currently disabled. Please check back later.</p>
</div>
@elseif(!$heroSmsConfigured)
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-16 h-16 rounded-2xl bg-yellow-900/40 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <h2 class="text-xl font-semibold text-white mb-2">Server 2 Not Configured</h2>
    <p class="text-slate-400 max-w-sm">HeroSMS API hasn't been configured yet. Please contact support.</p>
</div>
@else

{{-- ── Top bar ────────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-brand/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <p class="font-bold text-white text-base">Virtual Numbers</p>
                <span class="text-xs font-semibold bg-brand/20 text-brand px-2 py-0.5 rounded-full border border-brand/30">Server 2 — HeroSMS</span>
            </div>
            <p class="text-xs text-slate-400">Receive SMS codes for any service worldwide</p>
        </div>
    </div>
    <div class="flex items-center gap-3 bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5">
        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <span class="text-sm text-slate-400">Balance:</span>
        <span class="font-bold text-white" id="wallet-display">₦{{ number_format($wallet->balance, 2) }}</span>
        <a href="{{ route('dashboard.wallet') }}" class="text-xs text-brand hover:underline ml-1">Top up</a>
    </div>
</div>

{{-- ── Tabs ──────────────────────────────────────────────────────────────── --}}
<div class="flex items-center gap-1 border-b border-slate-700 mb-6">
    <button onclick="switchTab('browse')" id="tab-browse"
        class="tab-btn flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 border-brand text-brand transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        Available Services
    </button>
    <button onclick="switchTab('active')" id="tab-active"
        class="tab-btn flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        Active Rentals
        @if($activeOrders->count())
        <span id="active-badge" class="bg-brand text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $activeOrders->count() }}</span>
        @endif
    </button>
    <button onclick="switchTab('history')" id="tab-history"
        class="tab-btn flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Rental History
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB: AVAILABLE SERVICES
════════════════════════════════════════════════════════════ --}}
<div id="pane-browse">

    {{-- Filters row --}}
    <div class="flex flex-wrap gap-3 mb-5">
        {{-- Search --}}
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input id="svc-search" type="text" placeholder="Search services…" oninput="handleSearchInput()"
                style="font-size:16px"
                class="w-full pl-9 pr-8 py-2 bg-slate-800 border border-slate-700 text-white rounded-xl text-sm focus:outline-none focus:border-brand placeholder-slate-500">
            <button id="svc-search-clear" onclick="clearSearch()" class="hidden absolute right-2.5 top-2.5 text-slate-500 hover:text-white transition-colors" title="Clear search">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Country dropdown --}}
        <div class="relative">
            <select id="country-select" onchange="loadServices()"
                class="appearance-none bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2 pr-8 text-sm focus:outline-none focus:border-brand">
                <option value="">All Countries</option>
            </select>
            <svg class="pointer-events-none absolute right-2.5 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        {{-- Sort --}}
        <div class="relative">
            <select id="svc-sort" onchange="applyFilter()"
                class="appearance-none bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2 pr-8 text-sm focus:outline-none focus:border-brand">
                <option value="name">Sort: A–Z</option>
                <option value="price_asc">Price: Low–High</option>
                <option value="price_desc">Price: High–Low</option>
                <option value="count_desc">Stock: High–Low</option>
            </select>
            <svg class="pointer-events-none absolute right-2.5 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <span id="svc-count" class="self-center text-xs text-slate-400 whitespace-nowrap"></span>
    </div>

    {{-- Services state --}}
    <div id="svc-state" class="flex flex-col items-center justify-center py-24 bg-slate-800/40 rounded-2xl border border-slate-700">
        <div class="w-10 h-10 border-4 border-brand border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-slate-400 text-sm">Loading all services…</p>
    </div>

    {{-- Services grid (populated by JS) --}}
    <div id="svc-grid" class="hidden space-y-6"></div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB: ACTIVE RENTALS
════════════════════════════════════════════════════════════ --}}
<div id="pane-active" class="hidden">
    @if($activeOrders->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 bg-slate-800/40 rounded-2xl border border-slate-700">
        <svg class="w-10 h-10 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        <p class="text-white font-semibold mb-1">No active rentals</p>
        <p class="text-slate-400 text-sm">Order a number from Available Services to get started.</p>
        <button onclick="switchTab('browse')" class="mt-4 px-4 py-2 bg-brand text-white rounded-xl text-sm font-semibold">Browse Services</button>
    </div>
    @else
    <div class="space-y-3" id="active-orders-list">
        @foreach($activeOrders as $order)
        <div id="active-card-{{ $order->id }}"
            class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse flex-shrink-0"></span>
                <p class="font-bold text-white capitalize truncate">{{ $order->service }}</p>
                @if($order->country)
                <span class="text-xs text-slate-400 uppercase flex-shrink-0">({{ $order->country }})</span>
                @endif
                <span class="ml-auto text-xs text-slate-500 flex-shrink-0">₦{{ number_format($order->cost, 2) }}</span>
            </div>
            <div class="flex items-center gap-2 bg-slate-700/40 rounded-xl px-3 py-2.5">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span class="font-mono text-base text-brand flex-1 select-all min-w-0 truncate" id="phone-{{ $order->id }}">{{ $order->phone_number ?? 'Assigning…' }}</span>
                <button onclick="copyText('phone-{{ $order->id }}', this)" title="Copy number"
                    class="flex-shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-brand hover:bg-brand/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
            <div class="flex items-center gap-2 bg-slate-700/40 rounded-xl px-3 py-2.5">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-400 leading-none mb-0.5">SMS Code</p>
                    <p id="sms-code-{{ $order->id }}" class="font-mono font-bold text-lg text-green-400 tracking-widest leading-tight">{{ $order->sms_code ?? '—' }}</p>
                </div>
                <button onclick="copyText('sms-code-{{ $order->id }}', this)" title="Copy code"
                    class="flex-shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-green-400 hover:bg-green-400/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <p id="poll-status-{{ $order->id }}" class="text-xs text-slate-500">Auto-checking…</p>
                <p class="text-xs text-slate-600">{{ $order->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="checkSmsOnce({{ $order->id }}, this)"
                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-brand/10 hover:bg-brand/20 text-brand border border-brand/30 rounded-xl text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Check Now
                </button>
                <form method="POST" action="{{ route('dashboard.virtual-numbers.cancel', $order->id) }}"
                    onsubmit="return confirm('Cancel this rental?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 px-3 py-2 bg-red-900/20 hover:bg-red-900/40 text-red-400 border border-red-700/30 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancel
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <p class="text-xs text-slate-500 text-center mt-4">SMS codes are checked automatically every 5 seconds.</p>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     TAB: RENTAL HISTORY
════════════════════════════════════════════════════════════ --}}
<div id="pane-history" class="hidden">
    @if($historyOrders->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 bg-slate-800/40 rounded-2xl border border-slate-700">
        <svg class="w-10 h-10 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-slate-400 text-sm">No rental history yet.</p>
    </div>
    @else
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700 text-left">
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">Service</th>
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">Number</th>
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">SMS Code</th>
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">Cost</th>
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">Status</th>
                    <th class="px-5 py-3 text-xs text-slate-400 font-medium">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @foreach($historyOrders as $order)
                <tr class="hover:bg-slate-700/30 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-medium text-white capitalize">{{ $order->service }}</p>
                        @if($order->country)<p class="text-xs text-slate-500 uppercase">{{ $order->country }}</p>@endif
                    </td>
                    <td class="px-5 py-3 font-mono text-sm text-slate-300">{{ $order->phone_number ?? '—' }}</td>
                    <td class="px-5 py-3 font-mono font-bold text-green-400">{{ $order->sms_code ?? '—' }}</td>
                    <td class="px-5 py-3 text-white">₦{{ number_format($order->cost, 2) }}</td>
                    <td class="px-5 py-3">
                        @php $badge = match($order->status) {
                            'completed' => 'bg-green-900/50 text-green-300 border-green-700/50',
                            'cancelled' => 'bg-slate-700/50 text-slate-400 border-slate-600',
                            default     => 'bg-yellow-900/50 text-yellow-300 border-yellow-700/50',
                        }; @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs border {{ $badge }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-400">{{ $order->created_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     CONFIRMATION MODAL
════════════════════════════════════════════════════════════ --}}
<div id="rent-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-sm bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-6 z-10">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-brand/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div>
                <p class="font-bold text-white text-base">Rent a Number</p>
                <p class="text-xs text-slate-400">via HeroSMS — Server 2</p>
            </div>
        </div>
        <div class="bg-slate-800 rounded-xl p-4 mb-5 space-y-2.5">
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-400">Service</span>
                <span id="modal-svc-name" class="text-sm font-semibold text-white text-right max-w-[180px]"></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-400">Country</span>
                <span id="modal-country" class="text-sm text-white"></span>
            </div>
            <div class="border-t border-slate-700 pt-2.5 flex justify-between items-center">
                <span class="text-sm text-slate-400">Cost</span>
                <span id="modal-price" class="text-lg font-bold text-white"></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-400">Your balance</span>
                <span id="modal-balance" class="text-sm font-semibold text-green-400"></span>
            </div>
        </div>
        <p id="modal-warn" class="hidden text-xs text-red-400 bg-red-900/20 border border-red-700/30 rounded-lg p-2 mb-4">
            ⚠️ Insufficient balance. Please top up your wallet first.
        </p>
        <form method="POST" action="{{ route('dashboard.virtual-numbers.order') }}" id="rent-form">
            @csrf
            <input type="hidden" name="provider"     value="herosms">
            <input type="hidden" name="server"       value="server2">
            <input type="hidden" name="service_id"   id="f-service-id">
            <input type="hidden" name="country"      id="f-country">
            <input type="hidden" name="price"        id="f-price">
            <input type="hidden" name="service_name" id="f-svc-name">
            <button type="submit" id="rent-confirm-btn"
                class="w-full py-3 rounded-xl font-bold text-white text-sm flex items-center justify-center gap-2 transition-all"
                style="background: linear-gradient(135deg, #f97316, #ea580c)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Rent Number
            </button>
        </form>
        <p class="text-xs text-slate-500 text-center mt-3">Valid for ~20 min to receive one SMS code</p>
    </div>
</div>

@endif

<style>
.service-card { transition: transform 0.15s, box-shadow 0.15s; }
.service-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
.rent-btn {
    background: linear-gradient(135deg, #f97316, #ea580c);
    transition: opacity 0.15s, transform 0.1s;
}
.rent-btn:hover { opacity: 0.9; transform: scale(1.02); }
.rent-btn:active { transform: scale(0.98); }
</style>

@push('scripts')
<script>
const COUNTRIES_URL   = '{{ route('dashboard.virtual-numbers.countries') }}';
const SERVICES_URL    = '{{ route('dashboard.virtual-numbers.services') }}';
const PROVIDER        = 'herosms';
const USD_TO_NGN      = {{ $usdToNgn }};
const COMM_TYPE       = '{{ $commissionType }}';
const COMM_VALUE      = {{ $commissionValue }};
let allServices       = [];
let walletBalance     = {{ $wallet->balance }};
let countriesCache    = {};
let pollInterval      = null;

// ── Tab switching ─────────────────────────────────────────────────────────────
function switchTab(tab) {
    ['browse','active','history'].forEach(t => {
        document.getElementById('pane-' + t)?.classList.add('hidden');
        const btn = document.getElementById('tab-' + t);
        if (btn) {
            btn.classList.remove('border-brand','text-brand');
            btn.classList.add('border-transparent','text-slate-400');
        }
    });
    document.getElementById('pane-' + tab)?.classList.remove('hidden');
    const active = document.getElementById('tab-' + tab);
    if (active) {
        active.classList.add('border-brand','text-brand');
        active.classList.remove('border-transparent','text-slate-400');
    }
    if (tab === 'active') startPolling();
    else stopPolling();
}

// ── Load countries ────────────────────────────────────────────────────────────
async function loadCountries() {
    showState('loading');
    try {
        const res  = await fetch(COUNTRIES_URL + '?provider=' + PROVIDER, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) { showState('error', 'API error (' + res.status + ').'); return; }
        const data = await res.json();

        if (data.success && data.data?.length) {
            const sel = document.getElementById('country-select');
            sel.innerHTML = '<option value="">— All Countries —</option>';
            data.data.forEach(c => {
                const code = String(c.code ?? c.id ?? '');
                countriesCache[code] = { name: c.name, iso: c.iso || '' };
                const opt = document.createElement('option');
                opt.value = code;
                opt.textContent = c.name;
                sel.appendChild(opt);
            });
            loadServices();
        } else {
            showState('empty', data.message || 'No countries available.');
        }
    } catch(e) {
        console.error('Countries error:', e);
        showState('error', 'Could not load countries.');
    }
}

// ── Load services ─────────────────────────────────────────────────────────────
async function loadServices() {
    showState('loading');
    const country = document.getElementById('country-select').value;

    let url = SERVICES_URL + '?provider=' + PROVIDER;
    if (country) url += '&country=' + encodeURIComponent(country);

    try {
        const res  = await fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) { showState('error', 'API error (' + res.status + ').'); return; }
        const data = await res.json();

        if (!data.success) {
            showState('empty', data.message || 'No services available.');
            return;
        }

        const selectedName = country ? (countriesCache[country]?.name || 'Selected Country') : 'All Countries';

        allServices = (data.data || []).map(s => ({
            serviceId:   String(s.serviceId ?? ''),
            name:        s.name ?? s.serviceId ?? '',
            apiPrice:    parseFloat(s.cost_ngn ?? (parseFloat(s.cost_usd ?? s.cost ?? 0) * USD_TO_NGN)),
            costUsd:     parseFloat(s.cost_usd ?? s.cost ?? 0),
            count:       parseInt(s.count ?? 0, 10),
            country:     selectedName,
            countryCode: country || '',
        }));

        if (allServices.length) {
            applyFilter();
        } else {
            showState('empty', 'No services available for this selection.');
        }
    } catch(e) {
        console.error('Services error:', e);
        showState('error', 'Could not load services. Please try again.');
    }
}

// ── Search helpers ────────────────────────────────────────────────────────────
let searchDebounceTimer = null;
function handleSearchInput() {
    const q = document.getElementById('svc-search').value;
    document.getElementById('svc-search-clear').classList.toggle('hidden', q.length === 0);
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(applyFilter, 200);
}
function clearSearch() {
    document.getElementById('svc-search').value = '';
    document.getElementById('svc-search-clear').classList.add('hidden');
    applyFilter();
    document.getElementById('svc-search').focus();
}

// ── Filter + render ───────────────────────────────────────────────────────────
function applyFilter() {
    const q    = document.getElementById('svc-search').value.toLowerCase().trim();
    const sort = document.getElementById('svc-sort').value;

    let list = allServices.filter(s => {
        if (!q) return true;
        return (s.name ?? '').toLowerCase().includes(q) || (s.country ?? '').toLowerCase().includes(q);
    });

    if (sort === 'price_asc')   list.sort((a,b) => (a.apiPrice||0) - (b.apiPrice||0));
    else if (sort === 'price_desc') list.sort((a,b) => (b.apiPrice||0) - (a.apiPrice||0));
    else if (sort === 'count_desc') list.sort((a,b) => (b.count||0) - (a.count||0));
    else list.sort((a,b) => (a.name||'').localeCompare(b.name||''));

    renderServices(list);
}

function renderServices(list) {
    const grid  = document.getElementById('svc-grid');
    const state = document.getElementById('svc-state');
    const count = document.getElementById('svc-count');

    if (!list.length) {
        grid.classList.add('hidden');
        state.classList.remove('hidden');
        state.innerHTML = `
            <svg class="w-10 h-10 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <p class="text-slate-400 text-sm">No services match your search.</p>`;
        count.textContent = '';
        return;
    }

    state.classList.add('hidden');
    grid.classList.remove('hidden');
    count.textContent = list.length + ' service' + (list.length !== 1 ? 's' : '');

    // When a country is selected, show flat grid; otherwise group by name initial
    const country = document.getElementById('country-select').value;
    if (country) {
        // Single country view — flat grid
        const countryName = countriesCache[country]?.name || 'Selected Country';
        const iso = countriesCache[country]?.iso || '';
        const emoji = flagEmoji(iso);
        grid.innerHTML = `
        <div>
            <div class="flex items-center gap-2 mb-2 px-1">
                <span class="text-base">${emoji}</span>
                <h3 class="font-semibold text-slate-300 text-sm">${escHtml(countryName)}</h3>
                <span class="text-[11px] bg-slate-700/60 text-slate-400 px-2 py-0.5 rounded-full">${list.length}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                ${list.map(s => buildCard(s, countryName, emoji)).join('')}
            </div>
        </div>`;
    } else {
        // All countries — flat grid, no grouping
        grid.innerHTML = `
        <div>
            <div class="flex items-center gap-2 mb-2 px-1">
                <span class="text-base">🌍</span>
                <h3 class="font-semibold text-slate-300 text-sm">All Countries</h3>
                <span class="text-[11px] bg-slate-700/60 text-slate-400 px-2 py-0.5 rounded-full">${list.length}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                ${list.map(s => buildCard(s, 'All Countries', '🌍')).join('')}
            </div>
        </div>`;
    }
}

const AVATAR_COLORS = [
    '#6366f1','#8b5cf6','#ec4899','#f59e0b',
    '#10b981','#3b82f6','#ef4444','#14b8a6',
    '#f97316','#06b6d4','#a855f7','#84cc16',
];
function avatarColor(str) {
    const h = (str||'').split('').reduce((a,c)=>a+c.charCodeAt(0),0);
    return AVATAR_COLORS[h % AVATAR_COLORS.length];
}
function flagEmoji(iso) {
    if (!iso || iso.length !== 2) return '🌍';
    return iso.toUpperCase().split('').map(c => String.fromCodePoint(c.charCodeAt(0) - 65 + 0x1F1E6)).join('');
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function calcCommission(price) {
    if (COMM_VALUE <= 0) return 0;
    return COMM_TYPE === 'percent' ? Math.round(price * COMM_VALUE / 100 * 100) / 100 : COMM_VALUE;
}

function buildCard(s) {
    const id       = s.serviceId ?? '';
    const name     = s.name ?? id;
    const apiPrice = parseFloat(s.apiPrice ?? 0);
    const comm     = calcCommission(apiPrice);
    const total    = Math.round((apiPrice + comm) * 100) / 100;
    const stock    = parseInt(s.count ?? 0, 10);
    const color    = avatarColor(id);
    const initial  = (name[0] || '?').toUpperCase();
    const priceStr = total > 0
        ? '₦' + total.toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 0})
        : 'Free';

    const stockColor = stock > 100 ? 'text-emerald-400 bg-emerald-400/10'
                     : stock > 10  ? 'text-yellow-400 bg-yellow-400/10'
                     : stock > 0   ? 'text-red-400 bg-red-400/10'
                     : 'text-slate-500 bg-slate-700/40';
    const stockLabel = stock > 0 ? stock.toLocaleString() + ' pcs' : 'Check live';

    return `
    <div class="service-card group flex items-center gap-3 bg-[#0d1526] border border-slate-700/40 rounded-xl px-4 py-3 hover:border-brand/50 hover:bg-[#111d35] transition-all cursor-default">
        <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-white font-bold text-sm select-none" style="background:${color}20;color:${color};border:1.5px solid ${color}40">
            ${initial}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-white text-[13px] truncate leading-tight">${escHtml(name)}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span class="text-[11px] font-medium px-1.5 py-px rounded-full ${stockColor}">${stockLabel}</span>
                <span class="text-slate-700 text-[10px]">·</span>
                <span class="text-[11px] text-slate-500 font-mono">~${s.costUsd > 0 ? '$' + parseFloat(s.costUsd).toFixed(2) : 'free'}</span>
            </div>
        </div>
        <div class="flex-shrink-0 flex flex-col items-end gap-1.5">
            <span class="text-brand font-bold text-sm tabular-nums">${priceStr}</span>
            <button onclick="openModal('${escHtml(id)}','${escHtml(name)}',${apiPrice},'${escHtml(s.country||'')}','${escHtml(s.countryCode||'')}')"
                class="rent-btn text-white text-[11px] font-bold px-3 py-1 rounded-lg flex items-center gap-1 whitespace-nowrap">
                Buy
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>`;
}

// ── State placeholder ─────────────────────────────────────────────────────────
function showState(type, msg) {
    const grid  = document.getElementById('svc-grid');
    const state = document.getElementById('svc-state');
    grid.classList.add('hidden');
    state.classList.remove('hidden');
    document.getElementById('svc-count').textContent = '';
    if (type === 'loading') {
        state.innerHTML = `<div class="w-10 h-10 border-4 border-brand border-t-transparent rounded-full animate-spin mb-4"></div><p class="text-slate-400 text-sm">Loading services…</p>`;
    } else if (type === 'empty') {
        state.innerHTML = `<svg class="w-10 h-10 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-slate-400 text-sm">${escHtml(msg||'No services available.')}</p><button onclick="loadServices()" class="mt-3 text-xs text-brand hover:underline">Retry</button>`;
    } else {
        state.innerHTML = `<svg class="w-10 h-10 text-red-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><p class="text-red-400 text-sm">${escHtml(msg||'Error loading services.')}</p><button onclick="loadServices()" class="mt-3 text-xs text-brand hover:underline">Retry</button>`;
    }
}

// ── Confirmation modal ────────────────────────────────────────────────────────
function openModal(serviceId, serviceName, price, country, countryCode) {
    const comm  = calcCommission(price);
    const total = Math.round((price + comm) * 100) / 100;

    document.getElementById('modal-svc-name').textContent = serviceName;
    document.getElementById('modal-country').textContent  = country || 'All Countries';
    document.getElementById('modal-price').textContent    = total > 0 ? '₦' + total.toLocaleString('en-NG', {minimumFractionDigits: 2}) : 'Free';
    document.getElementById('modal-balance').textContent  = '₦' + walletBalance.toLocaleString('en-NG', {minimumFractionDigits: 2});

    const warn = document.getElementById('modal-warn');
    const btn  = document.getElementById('rent-confirm-btn');
    if (total > walletBalance) {
        warn.classList.remove('hidden');
        btn.disabled = true;
        btn.classList.add('opacity-50','cursor-not-allowed');
    } else {
        warn.classList.add('hidden');
        btn.disabled = false;
        btn.classList.remove('opacity-50','cursor-not-allowed');
    }

    document.getElementById('f-service-id').value = serviceId;
    document.getElementById('f-country').value    = countryCode;
    document.getElementById('f-price').value      = price;
    document.getElementById('f-svc-name').value   = serviceName;

    const modal = document.getElementById('rent-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    const modal = document.getElementById('rent-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('rent-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('rent-confirm-btn');
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Processing…';
    btn.disabled = true;
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ── SMS polling ───────────────────────────────────────────────────────────────
const activeOrderIds = [{{ $activeOrders->pluck('id')->join(', ') }}];

async function checkSmsOnce(orderId, btn) {
    const orig = btn?.innerHTML;
    if (btn) { btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Checking…'; btn.disabled = true; }
    try {
        const res  = await fetch(`/dashboard/virtual-numbers/${orderId}/sms`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            const codeEl   = document.getElementById('sms-code-' + orderId);
            const statusEl = document.getElementById('poll-status-' + orderId);
            if (data.sms_code && codeEl) {
                codeEl.textContent = data.sms_code;
                codeEl.classList.add('animate-pulse');
                setTimeout(() => codeEl.classList.remove('animate-pulse'), 3000);
            }
            if (statusEl) {
                if (data.status === 'completed') statusEl.textContent = '✅ SMS received!';
                else if (data.status === 'cancelled') statusEl.textContent = '❌ Cancelled';
                else statusEl.textContent = '⏳ Waiting for SMS…';
            }
            if (data.status === 'completed' || data.status === 'cancelled') {
                document.getElementById('active-card-' + orderId)?.classList.add('opacity-50');
            }
        }
    } catch(e) { console.error('SMS check error:', e); }
    finally {
        if (btn) { btn.innerHTML = orig; btn.disabled = false; }
    }
}

async function pollActiveOrders() {
    for (const id of activeOrderIds) {
        try {
            const res  = await fetch(`/dashboard/virtual-numbers/${id}/sms`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                const codeEl   = document.getElementById('sms-code-' + id);
                const statusEl = document.getElementById('poll-status-' + id);
                if (data.sms_code && codeEl && !codeEl.textContent.trim().replace('—','')) {
                    codeEl.textContent = data.sms_code;
                }
                if (statusEl) {
                    if (data.status === 'completed') statusEl.textContent = '✅ SMS received!';
                    else if (data.status === 'cancelled') statusEl.textContent = '❌ Cancelled';
                    else statusEl.textContent = '⏳ Waiting for SMS…';
                }
            }
        } catch(e) { /* ignore */ }
    }
}
function startPolling() {
    if (activeOrderIds.length && !pollInterval) {
        pollActiveOrders();
        pollInterval = setInterval(pollActiveOrders, 5000);
    }
}
function stopPolling() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
}

// ── Copy helper ───────────────────────────────────────────────────────────────
function copyText(elId, btn) {
    const el = document.getElementById(elId);
    if (!el) return;
    const text = el.textContent.trim();
    if (!text || text === '—') return;
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}

// ── Init ──────────────────────────────────────────────────────────────────────
loadCountries();
if (activeOrderIds.length) {
    startPolling();
}
</script>
@endpush
@endsection
