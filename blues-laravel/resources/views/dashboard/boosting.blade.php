@extends('layouts.dashboard')
@section('title', 'SMM Boosting')
@section('page-title', 'SMM Boosting')

@section('content')

<div class="max-w-5xl mx-auto">

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Social Media Boosting</h2>
        <p class="text-slate-400 text-sm mt-1">1000+ services across all platforms</p>
    </div>
    <a href="{{ route('dashboard.boosting-orders') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        My Orders
    </a>
</div>

{{-- Wallet balance --}}
<div class="bg-gradient-to-r from-orange-500/10 to-orange-900/20 border border-orange-500/20 rounded-xl p-5 mb-8 flex items-center justify-between">
    <div>
        <p class="text-sm text-slate-400">Your Wallet Balance</p>
        <p class="text-2xl font-bold text-white mt-0.5">₦{{ number_format($wallet->balance, 2) }}</p>
    </div>
    <a href="{{ route('dashboard.wallet') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Top Up
    </a>
</div>

@if($error)
<div class="bg-yellow-900/30 border border-yellow-700/50 rounded-xl p-5 mb-8 flex items-start gap-3">
    <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <p class="text-yellow-300 font-semibold text-sm">Service Unavailable</p>
        <p class="text-yellow-400 text-xs mt-0.5">{{ $error }}</p>
    </div>
</div>
@else

{{-- Search --}}
<div class="relative mb-6">
    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    <input type="text" id="service-search" placeholder="Search services (e.g. Instagram followers, YouTube views…)"
        class="w-full bg-slate-800 border border-slate-600 rounded-xl pl-10 pr-4 py-3 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-brand">
</div>

{{-- Category filter --}}
@if(count($categories))
<div class="flex gap-2 overflow-x-auto pb-3 mb-6 scrollbar-hide">
    <button onclick="filterCategory('all')" id="cat-all" class="cat-btn active shrink-0 px-4 py-1.5 rounded-lg text-xs font-semibold transition-colors bg-brand text-white">
        All Services
    </button>
    @foreach(array_keys($categories) as $cat)
    <button onclick="filterCategory('{{ addslashes($cat) }}')" id="cat-{{ Str::slug($cat) }}"
        class="cat-btn shrink-0 px-4 py-1.5 rounded-lg text-xs font-semibold transition-colors bg-slate-700 hover:bg-slate-600 text-slate-300">
        {{ $cat }}
    </button>
    @endforeach
</div>

{{-- Services list --}}
<div id="services-container" class="space-y-4">
    @foreach($categories as $catName => $services)
    <div class="cat-section" data-cat="{{ $catName }}">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            <span class="w-2 h-2 bg-brand rounded-full"></span>
            {{ $catName }}
        </h3>
        <div class="space-y-2">
            @foreach($services as $svc)
            <div class="service-row bg-slate-800 border border-slate-700 rounded-xl p-4 hover:border-brand/40 transition-all cursor-pointer"
                onclick="selectService({{ $svc['service'] }}, '{{ addslashes($svc['name']) }}', '{{ addslashes($catName) }}', {{ $svc['rate'] }}, {{ $svc['min'] }}, {{ $svc['max'] }})"
                data-name="{{ strtolower($svc['name']) }}" data-cat="{{ $catName }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $svc['name'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">Min: {{ number_format($svc['min']) }} &nbsp;|&nbsp; Max: {{ number_format($svc['max']) }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-brand font-bold text-sm">₦{{ number_format($svc['rate'] * \App\Models\Setting::get('usd_to_ngn_rate', 1600) / 1000, 2) }}</p>
                        <p class="text-xs text-slate-500">per 1000</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

@endif

{{-- Order Modal --}}
<div id="order-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-700">
            <h3 class="font-bold text-white">Place Order</h3>
            <button onclick="closeOrderModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('dashboard.boosting.order') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="service_id"   id="modal-service-id">
            <input type="hidden" name="service_name" id="modal-service-name">
            <input type="hidden" name="category"     id="modal-category">
            <input type="hidden" name="charge"       id="modal-charge">

            <div class="bg-slate-700/50 rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">Selected Service</p>
                <p id="modal-svc-name-display" class="text-sm font-semibold text-white"></p>
                <p id="modal-svc-rate-display" class="text-xs text-brand mt-1"></p>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Link / URL <span class="text-red-400">*</span></label>
                <input type="url" name="link" id="modal-link" required placeholder="https://www.instagram.com/your_profile"
                    class="w-full bg-slate-900 border border-slate-600 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-brand">
                <p class="text-xs text-slate-500 mt-1">Enter the full URL of the profile, post, or video.</p>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Quantity <span class="text-red-400">*</span></label>
                <input type="number" name="quantity" id="modal-quantity" required min="1"
                    placeholder="Enter quantity"
                    oninput="updateTotal()"
                    class="w-full bg-slate-900 border border-slate-600 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-brand">
                <p id="modal-qty-hint" class="text-xs text-slate-500 mt-1"></p>
            </div>

            {{-- Summary --}}
            <div id="order-summary" class="hidden bg-slate-700/50 border border-slate-600 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Price per 1000</span>
                    <span id="summary-rate" class="text-white font-medium"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Quantity</span>
                    <span id="summary-qty" class="text-white font-medium"></span>
                </div>
                <div class="flex justify-between text-sm font-bold border-t border-slate-600 pt-2 mt-2">
                    <span class="text-white">Total Cost</span>
                    <span id="summary-total" class="text-brand"></span>
                </div>
                <div id="balance-warning" class="hidden text-xs text-red-400 mt-1">
                    ⚠ Insufficient balance. <a href="{{ route('dashboard.wallet') }}" class="underline">Top up wallet →</a>
                </div>
            </div>

            <button type="submit" id="place-order-btn" disabled
                class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3 rounded-xl text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Place Order
            </button>
        </form>
    </div>
</div>

@endif
</div>

@endsection

@push('scripts')
<script>
const walletBalance = {{ $wallet->balance }};
const usdToNgn = {{ \App\Models\Setting::get('usd_to_ngn_rate', 1600) }};
const markupPct = {{ \App\Models\Setting::get('boosting_markup_percent', 20) }};

let selectedRate  = 0;
let selectedMin   = 1;
let selectedMax   = 99999999;

function selectService(id, name, category, rateUsdPer1000, min, max) {
    document.getElementById('modal-service-id').value   = id;
    document.getElementById('modal-service-name').value = name;
    document.getElementById('modal-category').value     = category;
    document.getElementById('modal-svc-name-display').textContent = name;

    const rateNgn = (rateUsdPer1000 * usdToNgn / 1000) * (1 + markupPct / 100);
    selectedRate = rateNgn;
    selectedMin  = min;
    selectedMax  = max;

    document.getElementById('modal-svc-rate-display').textContent = '₦' + rateNgn.toFixed(2) + ' per 1000';
    document.getElementById('modal-qty-hint').textContent = 'Min: ' + min.toLocaleString() + ' / Max: ' + max.toLocaleString();
    document.getElementById('modal-quantity').min   = min;
    document.getElementById('modal-quantity').max   = max;
    document.getElementById('modal-quantity').value = '';
    document.getElementById('order-summary').classList.add('hidden');
    document.getElementById('place-order-btn').disabled = true;
    document.getElementById('modal-link').value = '';
    document.getElementById('order-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('modal-link').focus(), 100);
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function updateTotal() {
    const qty = parseInt(document.getElementById('modal-quantity').value) || 0;
    if (qty < 1) { document.getElementById('order-summary').classList.add('hidden'); return; }

    const total = (selectedRate / 1000) * qty;
    document.getElementById('summary-rate').textContent  = '₦' + selectedRate.toFixed(2);
    document.getElementById('summary-qty').textContent   = qty.toLocaleString();
    document.getElementById('summary-total').textContent = '₦' + total.toFixed(2);
    document.getElementById('modal-charge').value        = total.toFixed(2);
    document.getElementById('order-summary').classList.remove('hidden');

    const warn = document.getElementById('balance-warning');
    const btn  = document.getElementById('place-order-btn');
    if (total > walletBalance) {
        warn.classList.remove('hidden');
        btn.disabled = true;
    } else if (qty >= selectedMin && qty <= selectedMax) {
        warn.classList.add('hidden');
        btn.disabled = false;
    } else {
        warn.classList.add('hidden');
        btn.disabled = true;
    }
}

// Search
document.getElementById('service-search').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.service-row').forEach(row => {
        const name = row.dataset.name || '';
        row.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
    document.querySelectorAll('.cat-section').forEach(sec => {
        const visible = Array.from(sec.querySelectorAll('.service-row')).some(r => r.style.display !== 'none');
        sec.style.display = visible ? '' : 'none';
    });
});

// Category filter
function filterCategory(cat) {
    document.querySelectorAll('.cat-btn').forEach(b => {
        b.classList.toggle('bg-brand', false);
        b.classList.toggle('text-white', false);
        b.classList.toggle('bg-slate-700', true);
        b.classList.toggle('text-slate-300', true);
    });
    const active = cat === 'all' ? document.getElementById('cat-all') : document.getElementById('cat-' + cat.toLowerCase().replace(/[^a-z0-9]+/g,'-'));
    if (active) {
        active.classList.add('bg-brand','text-white');
        active.classList.remove('bg-slate-700','text-slate-300');
    }
    document.querySelectorAll('.cat-section').forEach(sec => {
        sec.style.display = (cat === 'all' || sec.dataset.cat === cat) ? '' : 'none';
    });
}

document.getElementById('order-modal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOrderModal(); });
</script>
@endpush
