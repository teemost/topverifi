@extends('layouts.dashboard')
@section('title', 'My SMM Orders')
@section('page-title', 'My SMM Orders')

@section('content')

<div class="max-w-5xl mx-auto">

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">My SMM Orders</h2>
        <p class="text-slate-400 text-sm mt-1">Track all your social media boosting orders in real time</p>
    </div>
    <a href="{{ route('dashboard.boosting') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Order
    </a>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 bg-green-900/40 border border-green-700 rounded-xl text-green-300 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@if($orders->isEmpty())
<div class="bg-slate-800 border border-slate-700 rounded-2xl p-16 text-center">
    <div class="w-16 h-16 bg-slate-700 rounded-2xl mx-auto mb-4 flex items-center justify-center">
        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <h3 class="text-white font-semibold mb-2">No orders yet</h3>
    <p class="text-slate-400 text-sm mb-6">You haven't placed any SMM orders yet.</p>
    <a href="{{ route('dashboard.boosting') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors">
        Browse Services
    </a>
</div>
@else

{{-- Live status bar --}}
<div id="live-status-bar" class="hidden mb-5 px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl flex items-center justify-between gap-4">
    <div class="flex items-center gap-2.5">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand"></span>
        </span>
        <span class="text-sm text-slate-300 font-medium">Live tracking active</span>
        <span id="syncing-indicator" class="hidden text-xs text-brand animate-pulse">· syncing…</span>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-xs text-slate-500">Next refresh in <span id="refresh-countdown" class="text-slate-400 font-semibold tabular-nums">30</span>s</span>
        <button onclick="triggerPoll()" class="text-xs text-brand hover:text-brand-dark font-medium transition-colors flex items-center gap-1">
            <svg id="refresh-icon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh now
        </button>
    </div>
</div>

{{-- Table --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px]">
            <thead>
                <tr class="border-b border-slate-700 bg-slate-800/80">
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Service</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Link</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Qty</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Cost</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Progress</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700" id="orders-tbody">
                @foreach($orders as $order)
                @php
                    $isActive = in_array($order->status, ['pending', 'in_progress', 'processing']);
                    $progress = null;
                    if ($order->start_count && $order->quantity) {
                        $done = $order->quantity - max(0, $order->remains ?? $order->quantity);
                        $progress = round(($done / $order->quantity) * 100);
                    }
                @endphp
                <tr class="hover:bg-slate-700/30 transition-colors order-row {{ $isActive ? 'is-active' : '' }}" data-order-id="{{ $order->id }}">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-white max-w-[200px] truncate">{{ $order->service_name }}</p>
                        @if($order->category)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $order->category }}</p>
                        @endif
                        @if($order->jap_order_id)
                        <p class="text-xs text-slate-600 font-mono mt-0.5">JAP #{{ $order->jap_order_id }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ $order->link }}" target="_blank" class="text-xs text-brand hover:underline max-w-[110px] truncate block">
                            {{ parse_url($order->link, PHP_URL_HOST) ?? $order->link }}
                        </a>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-white font-medium">{{ number_format($order->quantity) }}</p>
                        <p class="order-remains text-xs text-slate-500 mt-0.5" data-order-id="{{ $order->id }}">
                            @if($order->remains !== null){{ number_format($order->remains) }} left@endif
                        </p>
                    </td>
                    <td class="px-4 py-4 text-sm text-white font-medium">₦{{ number_format($order->charge, 2) }}</td>
                    <td class="px-4 py-4">
                        <div class="w-24">
                            @if($progress !== null)
                            <div class="flex items-center gap-1.5">
                                <div class="flex-1 bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="order-progress-bar h-1.5 rounded-full bg-brand transition-all duration-700"
                                         data-order-id="{{ $order->id }}"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="order-progress-pct text-xs text-slate-400 tabular-nums w-8 text-right"
                                      data-order-id="{{ $order->id }}">{{ $progress }}%</span>
                            </div>
                            @else
                            <span class="order-progress-placeholder text-xs text-slate-600" data-order-id="{{ $order->id }}">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="order-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status_badge }}"
                              data-order-id="{{ $order->id }}">
                            @if($isActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                            @endif
                            <span class="order-status-label">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        </span>
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $orders->links() }}
</div>

@endif
</div>

@if(!$orders->isEmpty())
<script>
(function () {
    const POLL_URL   = "{{ route('dashboard.boosting.poll') }}";
    const INTERVAL   = 30000; // 30 seconds
    const ACTIVE_STATUSES = ['pending', 'in_progress', 'processing'];

    let pollTimer   = null;
    let countTimer  = null;
    let countdown   = 30;
    let polling     = false;

    // Badge class map (mirrors PHP getStatusBadgeAttribute)
    const badgeMap = {
        completed:   'bg-green-900/40 text-green-300 border-green-700',
        in_progress: 'bg-blue-900/40 text-blue-300 border-blue-700',
        processing:  'bg-blue-900/40 text-blue-300 border-blue-700',
        pending:     'bg-yellow-900/40 text-yellow-300 border-yellow-700',
        partial:     'bg-orange-900/40 text-orange-300 border-orange-700',
        cancelled:   'bg-red-900/40 text-red-300 border-red-700',
        canceled:    'bg-red-900/40 text-red-300 border-red-700',
    };

    function hasActiveOrders() {
        return document.querySelectorAll('.order-row.is-active').length > 0;
    }

    function startLiveBar() {
        const bar = document.getElementById('live-status-bar');
        if (bar) bar.classList.remove('hidden');
    }

    function stopLiveBar() {
        const bar = document.getElementById('live-status-bar');
        if (bar) bar.classList.add('hidden');
        clearInterval(pollTimer);
        clearInterval(countTimer);
    }

    function resetCountdown() {
        countdown = 30;
        const el = document.getElementById('refresh-countdown');
        if (el) el.textContent = countdown;
    }

    function startCountdown() {
        clearInterval(countTimer);
        resetCountdown();
        countTimer = setInterval(() => {
            countdown = Math.max(0, countdown - 1);
            const el = document.getElementById('refresh-countdown');
            if (el) el.textContent = countdown;
        }, 1000);
    }

    function applyOrderUpdate(data) {
        const row       = document.querySelector(`.order-row[data-order-id="${data.id}"]`);
        const badge     = document.querySelector(`.order-badge[data-order-id="${data.id}"]`);
        const remains   = document.querySelector(`.order-remains[data-order-id="${data.id}"]`);
        const progBar   = document.querySelector(`.order-progress-bar[data-order-id="${data.id}"]`);
        const progPct   = document.querySelector(`.order-progress-pct[data-order-id="${data.id}"]`);
        const progPlaceholder = document.querySelector(`.order-progress-placeholder[data-order-id="${data.id}"]`);

        if (!row) return;

        const isNowActive = ACTIVE_STATUSES.includes(data.status);

        // Update row active class
        if (isNowActive) {
            row.classList.add('is-active');
        } else {
            row.classList.remove('is-active');
        }

        // Update status badge
        if (badge) {
            const allBadgeClasses = Object.values(badgeMap).join(' ').split(' ');
            badge.classList.remove(...allBadgeClasses);
            const newClasses = (badgeMap[data.status] || 'bg-slate-700/40 text-slate-300 border-slate-600').split(' ');
            badge.classList.add(...newClasses);

            const pulseEl = badge.querySelector('span:first-child');
            const labelEl = badge.querySelector('.order-status-label');

            if (isNowActive) {
                if (!pulseEl || !pulseEl.classList.contains('animate-pulse')) {
                    const pulse = document.createElement('span');
                    pulse.className = 'w-1.5 h-1.5 rounded-full bg-current animate-pulse';
                    badge.insertBefore(pulse, badge.firstChild);
                }
            } else {
                const pulseSpan = badge.querySelector('span.animate-pulse');
                if (pulseSpan) pulseSpan.remove();
            }

            if (labelEl) labelEl.textContent = data.status_label;
        }

        // Update remains
        if (remains) {
            remains.textContent = data.remains !== null ? `${Number(data.remains).toLocaleString()} left` : '';
        }

        // Update progress bar
        if (data.progress !== null && data.progress !== undefined) {
            if (progBar) {
                progBar.style.width = data.progress + '%';
            }
            if (progPct) {
                progPct.textContent = data.progress + '%';
            }
            if (progPlaceholder) {
                // Upgrade placeholder to full progress bar
                const cell = progPlaceholder.parentElement;
                cell.innerHTML = `
                    <div class="flex items-center gap-1.5">
                        <div class="flex-1 bg-slate-700 rounded-full h-1.5 overflow-hidden">
                            <div class="order-progress-bar h-1.5 rounded-full bg-brand transition-all duration-700"
                                 data-order-id="${data.id}"
                                 style="width: ${data.progress}%"></div>
                        </div>
                        <span class="order-progress-pct text-xs text-slate-400 tabular-nums w-8 text-right"
                              data-order-id="${data.id}">${data.progress}%</span>
                    </div>`;
            }
        }
    }

    async function triggerPoll() {
        if (polling) return;
        polling = true;

        const syncEl = document.getElementById('syncing-indicator');
        const icon   = document.getElementById('refresh-icon');
        if (syncEl) syncEl.classList.remove('hidden');
        if (icon)   icon.classList.add('animate-spin');

        try {
            const res  = await fetch(POLL_URL, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();

            (json.orders || []).forEach(applyOrderUpdate);

            if (!json.has_active) {
                stopLiveBar();
            } else {
                resetCountdown();
                startCountdown();
            }
        } catch (e) {
            // silently skip
        } finally {
            polling = false;
            if (syncEl) syncEl.classList.add('hidden');
            if (icon)   icon.classList.remove('animate-spin');
        }
    }

    // Expose for "Refresh now" button
    window.triggerPoll = triggerPoll;

    // Boot
    if (hasActiveOrders()) {
        startLiveBar();
        startCountdown();
        pollTimer = setInterval(triggerPoll, INTERVAL);
    }
})();
</script>
@endif

@endsection
