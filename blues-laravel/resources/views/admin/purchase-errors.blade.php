@extends('layouts.admin')
@section('title', 'Purchase Error Logs')
@section('page-title', 'Purchase Error Logs')

@section('content')
<div class="space-y-6">

    {{-- ── Stats cards ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'Total',      'value' => $stats['total'],      'color' => 'text-white'],
            ['label' => 'Today',      'value' => $stats['today'],      'color' => 'text-yellow-400'],
            ['label' => 'Hero-SMS',   'value' => $stats['herosms'],    'color' => 'text-orange-400'],
            ['label' => 'GrizzlySMS', 'value' => $stats['grizzlysms'], 'color' => 'text-green-400'],
            ['label' => 'SMM Boost',  'value' => $stats['jap'],        'color' => 'text-purple-400'],
            ['label' => '5sim',       'value' => $stats['fivesim'],    'color' => 'text-blue-400'],
        ] as $card)
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold {{ $card['color'] }} mt-1">{{ number_format($card['value']) }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Filters ──────────────────────────────────────────────────────────── --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
        <form method="GET" action="{{ route('admin.purchase-errors') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search user or message…" class="lg:col-span-2">

            <select name="provider">
                <option value="">All providers</option>
                @foreach($providers as $p)
                    <option value="{{ $p }}" @selected(request('provider') === $p)>
                        {{ match($p) {
                            'herosms'    => 'Hero-SMS',
                            'grizzlysms' => 'GrizzlySMS',
                            'fivesim'    => '5sim',
                            'jap'        => 'SMM Boost',
                            default      => ucfirst($p),
                        } }}
                    </option>
                @endforeach
            </select>

            <select name="action">
                <option value="">All actions</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucfirst(str_replace('-', ' ', $a)) }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                @if(request()->hasAny(['search','provider','action','from','to']))
                    <a href="{{ route('admin.purchase-errors') }}" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg text-sm font-medium transition-colors">✕</a>
                @endif
            </div>

            {{-- Date range on its own row --}}
            <div class="sm:col-span-2 lg:col-span-2 flex gap-2 items-center">
                <input type="date" name="from" value="{{ request('from') }}" class="flex-1" title="From date">
                <span class="text-slate-500 text-sm shrink-0">to</span>
                <input type="date" name="to" value="{{ request('to') }}" class="flex-1" title="To date">
            </div>
        </form>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────────────── --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700 flex items-center justify-between flex-wrap gap-3">
            <h2 class="text-sm font-semibold text-white">
                {{ $logs->total() }} {{ Str::plural('error', $logs->total()) }}
                @if(request()->hasAny(['search','provider','action','from','to']))
                    <span class="text-slate-400 font-normal">— filtered</span>
                @endif
            </h2>
            @if($logs->total() > 0)
            <form method="POST" action="{{ route('admin.purchase-errors.clear') }}"
                  onsubmit="return confirm('Delete ALL matching error logs? This cannot be undone.')">
                @csrf
                @method('DELETE')
                @if(request('provider'))
                    <input type="hidden" name="provider" value="{{ request('provider') }}">
                @endif
                <button type="submit" class="btn-danger text-xs px-3 py-1.5">
                    Clear {{ request('provider') ? ucfirst(request('provider')).' ' : '' }}Logs
                </button>
            </form>
            @endif
        </div>

        @if($logs->isEmpty())
            <div class="py-20 text-center">
                <svg class="w-10 h-10 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-slate-400 text-sm">No error logs found.</p>
                @if(request()->hasAny(['search','provider','action','from','to']))
                    <a href="{{ route('admin.purchase-errors') }}" class="text-brand text-sm hover:underline mt-1 inline-block">Clear filters</a>
                @endif
            </div>
        @else
        <div class="table-scroll">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-700 text-left text-xs text-slate-400 uppercase tracking-wider">
                        <th class="px-5 py-3 font-medium">Time</th>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Provider</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                        <th class="px-4 py-3 font-medium">Error Message</th>
                        <th class="px-4 py-3 font-medium">Context</th>
                        <th class="px-4 py-3 font-medium w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-700/30 transition-colors group">
                        {{-- Time --}}
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="text-slate-300" title="{{ $log->created_at->toDateTimeString() }}">
                                {{ $log->created_at->format('M d, H:i') }}
                            </span>
                            <p class="text-xs text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                        </td>

                        {{-- User --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($log->user)
                                <a href="{{ route('admin.users') }}?search={{ urlencode($log->user->email) }}"
                                   class="text-slate-200 hover:text-brand transition-colors font-medium text-xs">
                                    {{ $log->user->name }}
                                </a>
                                <p class="text-xs text-slate-500 truncate max-w-[120px]">{{ $log->user->email }}</p>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Provider --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->provider_color }}">
                                {{ $log->provider_label }}
                            </span>
                        </td>

                        {{-- Action --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-slate-300 text-xs">{{ ucfirst(str_replace('-', ' ', $log->action)) }}</span>
                        </td>

                        {{-- Error message --}}
                        <td class="px-4 py-3 max-w-xs">
                            <p class="text-red-300 text-xs leading-relaxed line-clamp-2" title="{{ $log->error_message }}">
                                {{ $log->error_message }}
                            </p>
                            @if($log->ip_address)
                                <p class="text-slate-600 text-xs mt-0.5">{{ $log->ip_address }}</p>
                            @endif
                        </td>

                        {{-- Context --}}
                        <td class="px-4 py-3 max-w-xs">
                            @if($log->context)
                                <div class="space-y-0.5">
                                    @foreach($log->context as $k => $v)
                                        @if(!is_null($v) && $v !== '')
                                        <p class="text-xs text-slate-400">
                                            <span class="text-slate-500">{{ str_replace('_', ' ', $k) }}:</span>
                                            <span class="text-slate-300 font-mono">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                        </p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Delete --}}
                        <td class="px-4 py-3 text-right">
                            <form method="POST"
                                  action="{{ route('admin.purchase-errors.destroy', $log) }}"
                                  onsubmit="return confirm('Delete this error log entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="opacity-0 group-hover:opacity-100 transition-opacity text-slate-500 hover:text-red-400 p-1 rounded"
                                        title="Delete entry">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-slate-700">
            {{ $logs->links('pagination::simple-tailwind') }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
