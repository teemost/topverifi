@extends('layouts.admin')
@section('title','SMM Orders')
@section('page-title','SMM Orders')
@section('content')

{{-- Stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Total Orders</p>
        <p class="text-2xl font-bold text-white mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Pending</p>
        <p class="text-2xl font-bold text-yellow-400 mt-1">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">In Progress</p>
        <p class="text-2xl font-bold text-blue-400 mt-1">{{ number_format($stats['in_progress']) }}</p>
    </div>
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Completed</p>
        <p class="text-2xl font-bold text-green-400 mt-1">{{ number_format($stats['completed']) }}</p>
    </div>
</div>

{{-- JAP balance + actions --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        @if($japBalance !== null)
        <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg px-4 py-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span class="text-sm text-slate-300">JAP Balance: <strong class="text-white">${{ number_format($japBalance, 2) }}</strong></span>
        </div>
        @endif
    </div>
    <form method="POST" action="{{ route('admin.boosting-orders.sync-all') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Sync All Active Orders
        </button>
    </form>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user…"
        class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand">
    <select name="status" class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand">
        <option value="">All Statuses</option>
        @foreach(['pending','in_progress','completed','partial','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">Filter</button>
    @if(request('search') || request('status'))
    <a href="{{ route('admin.boosting-orders') }}" class="inline-flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <div class="table-scroll">
        <table class="w-full min-w-[700px]">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">ID</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">User</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Service</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Link</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Qty</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Charge</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Date</th>
                    <th class="px-4 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4 text-xs text-slate-400 font-mono">#{{ $order->id }}</td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-white font-medium">{{ $order->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-400">{{ $order->user->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-xs text-white font-medium max-w-[180px] truncate">{{ $order->service_name }}</p>
                        @if($order->jap_order_id)
                        <p class="text-xs text-slate-500 font-mono">JAP #{{ $order->jap_order_id }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ $order->link }}" target="_blank" class="text-xs text-brand hover:underline max-w-[120px] truncate block">
                            {{ parse_url($order->link, PHP_URL_HOST) ?? $order->link }}
                        </a>
                    </td>
                    <td class="px-4 py-4 text-sm text-white">{{ number_format($order->quantity) }}</td>
                    <td class="px-4 py-4 text-sm text-white font-medium">₦{{ number_format($order->charge, 2) }}</td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $order->created_at->format('M d') }}</td>
                    <td class="px-4 py-4">
                        @if($order->jap_order_id)
                        <form method="POST" action="{{ route('admin.boosting-orders.sync', $order->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-slate-400 hover:text-white transition-colors" title="Sync status">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center text-slate-400">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $orders->links() }}</div>

@endsection
