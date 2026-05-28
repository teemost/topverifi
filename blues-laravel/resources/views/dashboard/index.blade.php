@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')

{{-- Wallet Hero Card --}}
<div class="rounded-2xl p-6 mb-6 relative overflow-hidden" style="background:linear-gradient(135deg,#ea580c 0%,#f97316 60%,#fb923c 100%)">
    <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-10" style="background:radial-gradient(circle,#fff 0%,transparent 70%);transform:translate(30%,-30%)"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full opacity-10" style="background:radial-gradient(circle,#fff 0%,transparent 70%);transform:translate(-40%,40%)"></div>
    <div class="relative">
        <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mb-1">Wallet Balance</p>
        <p class="text-white font-black text-4xl sm:text-5xl tracking-tight mb-4">₦{{ number_format($wallet->balance, 2) }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard.wallet') }}"
               class="inline-flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold text-sm px-4 py-2 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Top Up
            </a>
            <a href="{{ route('dashboard.wallet') }}"
               class="inline-flex items-center gap-1.5 bg-black/15 hover:bg-black/25 text-white font-semibold text-sm px-4 py-2 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                History
            </a>
            <span class="ml-auto text-white/60 text-xs font-medium">TopVerifi Wallet</span>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-1">
        <div class="w-8 h-8 bg-orange-500/10 rounded-xl flex items-center justify-center mb-1">
            <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <p class="text-white font-bold text-xl leading-none">{{ $orderCount }}</p>
        <p class="text-slate-400 text-xs">SMM Orders</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-1">
        <div class="w-8 h-8 bg-green-500/10 rounded-xl flex items-center justify-center mb-1">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <p class="text-white font-bold text-xl leading-none">{{ $vnCount ?? 0 }}</p>
        <p class="text-slate-400 text-xs">Numbers</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-1">
        <div class="w-8 h-8 bg-purple-500/10 rounded-xl flex items-center justify-center mb-1">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-white font-bold text-xl leading-none">{{ $referralCount ?? 0 }}</p>
        <p class="text-slate-400 text-xs">Referrals</p>
    </div>
</div>

{{-- Quick Actions --}}
<p class="text-slate-500 text-xs uppercase tracking-wider font-semibold mb-3">Quick Actions</p>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('dashboard.virtual-numbers') }}"
       class="bg-slate-800 border border-slate-700 hover:border-green-500/40 hover:bg-slate-700/60 rounded-2xl p-4 flex flex-col items-center gap-2.5 text-center transition-all group">
        <div class="w-11 h-11 bg-green-500/10 group-hover:bg-green-500/20 rounded-2xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <span class="text-white text-xs font-semibold leading-tight">Virtual Numbers</span>
    </a>
    <a href="{{ route('dashboard.boosting') }}"
       class="bg-slate-800 border border-slate-700 hover:border-brand/40 hover:bg-slate-700/60 rounded-2xl p-4 flex flex-col items-center gap-2.5 text-center transition-all group">
        <div class="w-11 h-11 bg-orange-500/10 group-hover:bg-orange-500/20 rounded-2xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <span class="text-white text-xs font-semibold leading-tight">SMM Boosting</span>
    </a>
    <a href="{{ route('dashboard.boosting-orders') }}"
       class="bg-slate-800 border border-slate-700 hover:border-purple-500/40 hover:bg-slate-700/60 rounded-2xl p-4 flex flex-col items-center gap-2.5 text-center transition-all group">
        <div class="w-11 h-11 bg-purple-500/10 group-hover:bg-purple-500/20 rounded-2xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <span class="text-white text-xs font-semibold leading-tight">My Orders</span>
    </a>
    <a href="{{ route('dashboard.wallet') }}"
       class="bg-slate-800 border border-slate-700 hover:border-yellow-500/40 hover:bg-slate-700/60 rounded-2xl p-4 flex flex-col items-center gap-2.5 text-center transition-all group">
        <div class="w-11 h-11 bg-yellow-500/10 group-hover:bg-yellow-500/20 rounded-2xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <span class="text-white text-xs font-semibold leading-tight">Top Up</span>
    </a>
</div>

{{-- Referral Banner --}}
@php $dashProfile = Auth::user()->profile; @endphp
@if($dashProfile && $dashProfile->referral_code)
<div class="bg-gradient-to-r from-brand/10 to-purple-500/10 border border-brand/20 rounded-2xl p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-brand/15 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="min-w-0">
            <p class="text-white text-sm font-semibold">Refer friends &amp; earn bonuses</p>
            <p class="text-slate-400 text-xs mt-0.5 truncate">Your code: <span class="text-brand font-mono font-bold">{{ $dashProfile->referral_code }}</span></p>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <input id="dash-ref-link" type="text" readonly value="{{ url('/r/' . $dashProfile->referral_code) }}" class="w-1 h-1 opacity-0 absolute" tabindex="-1">
        <button onclick="copyDashReferral()" id="dash-copy-btn"
            class="bg-brand hover:bg-brand-dark text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
            Copy Link
        </button>
        <a href="{{ route('dashboard.referrals') }}" class="border border-slate-600 hover:border-brand text-slate-300 hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">Details</a>
    </div>
</div>
@endif

{{-- Recent Activity --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-4 bg-brand rounded-full"></div>
            <h2 class="font-semibold text-white text-sm">Recent Activity</h2>
        </div>
        <a href="{{ route('dashboard.boosting-orders') }}" class="text-xs text-brand hover:underline">View all →</a>
    </div>

    @forelse($recentOrders as $order)
    <div class="flex items-center gap-4 px-5 py-3.5 border-b border-slate-700/50 last:border-0 hover:bg-slate-700/30 transition-colors">
        {{-- Icon --}}
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
            {{ $order->status === 'completed' ? 'bg-green-500/10' : ($order->status === 'pending' ? 'bg-yellow-500/10' : 'bg-blue-500/10') }}">
            <svg class="w-4 h-4 {{ $order->status === 'completed' ? 'text-green-400' : ($order->status === 'pending' ? 'text-yellow-400' : 'text-blue-400') }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($order->status === 'completed')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @elseif($order->status === 'pending')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                @endif
            </svg>
        </div>
        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-medium truncate">{{ $order->service_name }}</p>
            <p class="text-slate-500 text-xs">{{ $order->created_at->diffForHumans() }}</p>
        </div>
        {{-- Right --}}
        <div class="text-right shrink-0">
            <p class="text-white text-sm font-semibold">₦{{ number_format($order->charge, 2) }}</p>
            <span class="text-xs font-medium
                {{ $order->status === 'completed' ? 'text-green-400' : ($order->status === 'pending' ? 'text-yellow-400' : 'text-blue-400') }}">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </div>
    </div>
    @empty
    <div class="px-5 py-12 text-center">
        <div class="w-12 h-12 bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <p class="text-slate-500 text-sm mb-1">No orders yet</p>
        <a href="{{ route('dashboard.boosting') }}" class="text-brand hover:underline text-sm">Browse SMM services →</a>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function copyDashReferral() {
    const val = document.getElementById('dash-ref-link').value;
    const btn = document.getElementById('dash-copy-btn');
    function markDone() {
        btn.textContent = '✓ Copied!';
        btn.classList.add('bg-green-600');
        btn.classList.remove('bg-brand', 'hover:bg-brand-dark');
        setTimeout(() => {
            btn.innerHTML = '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>Copy Link';
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-brand', 'hover:bg-brand-dark');
        }, 2200);
    }
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(val).then(markDone).catch(() => _fbCopy(val, markDone));
    } else {
        _fbCopy(val, markDone);
    }
}
function _fbCopy(text, cb) {
    const ta = document.createElement('textarea');
    ta.value = text; ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none;';
    document.body.appendChild(ta); ta.focus(); ta.select();
    try { document.execCommand('copy'); if (cb) cb(); } catch(e) {}
    document.body.removeChild(ta);
}
</script>
@endpush
