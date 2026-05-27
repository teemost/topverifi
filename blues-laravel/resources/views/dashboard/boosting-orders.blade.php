@extends('layouts.dashboard')
@section('title', 'My SMM Orders')
@section('page-title', 'My SMM Orders')

@section('content')

<div class="max-w-5xl mx-auto">

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">My SMM Orders</h2>
        <p class="text-slate-400 text-sm mt-1">Track all your social media boosting orders</p>
    </div>
    <a href="{{ route('dashboard.boosting') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Order
    </a>
</div>

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

{{-- Table --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="table-scroll">
        <table class="w-full min-w-[640px]">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-6 py-4">Service</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Link</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Qty</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Cost</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 py-4">Date</th>
                    <th class="px-4 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-700/30 transition-colors">
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
                        <a href="{{ $order->link }}" target="_blank" class="text-xs text-brand hover:underline max-w-[120px] truncate block">
                            {{ parse_url($order->link, PHP_URL_HOST) ?? $order->link }}
                        </a>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-white font-medium">{{ number_format($order->quantity) }}</p>
                        @if($order->remains !== null)
                        <p class="text-xs text-slate-500">{{ number_format($order->remains) }} left</p>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm text-white font-medium">₦{{ number_format($order->charge, 2) }}</td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $order->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-4">
                        @if($order->jap_order_id)
                        <form method="POST" action="{{ route('dashboard.boosting.sync', $order->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1" title="Refresh status">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Sync
                            </button>
                        </form>
                        @endif
                    </td>
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
@endsection
