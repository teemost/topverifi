<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseErrorLog;
use Illuminate\Http\Request;

class PurchaseErrorLogsController extends Controller
{
    /**
     * Display the paginated, filterable error log.
     */
    public function index(Request $request)
    {
        $query = PurchaseErrorLog::with('user')->latest();

        // Filter by provider
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by user name / email or error message
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('error_message', 'like', $term)
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'like', $term)->orWhere('email', 'like', $term)
                  );
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Summary stats for the header cards
        $stats = [
            'total'      => PurchaseErrorLog::count(),
            'today'      => PurchaseErrorLog::whereDate('created_at', today())->count(),
            'herosms'    => PurchaseErrorLog::where('provider', 'herosms')->count(),
            'grizzlysms' => PurchaseErrorLog::where('provider', 'grizzlysms')->count(),
            'jap'        => PurchaseErrorLog::where('provider', 'jap')->count(),
            'fivesim'    => PurchaseErrorLog::where('provider', 'fivesim')->count(),
        ];

        $providers = PurchaseErrorLog::distinct()->orderBy('provider')->pluck('provider');
        $actions   = PurchaseErrorLog::distinct()->orderBy('action')->pluck('action');

        return view('admin.purchase-errors', compact('logs', 'stats', 'providers', 'actions'));
    }

    /**
     * Delete a single error log entry.
     */
    public function destroy(PurchaseErrorLog $log)
    {
        $log->delete();
        return back()->with('success', 'Error log entry deleted.');
    }

    /**
     * Wipe all error logs (with an optional provider filter).
     */
    public function clearAll(Request $request)
    {
        $query = PurchaseErrorLog::query();

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
            $label = ucfirst($request->provider) . ' error logs';
        } else {
            $label = 'All error logs';
        }

        $count = $query->count();
        $query->delete();

        return back()->with('success', "{$label} cleared ({$count} " . str('entry')->plural($count) . " removed).");
    }
}
