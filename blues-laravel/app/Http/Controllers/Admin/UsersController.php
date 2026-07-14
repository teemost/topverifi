<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('wallet');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);
        Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        return back()->with('success', "User {$user->name} created successfully.");
    }

    public function updateStatus(Request $request, User $user)
    {
        $status = $request->status;
        if (!in_array($status, ['active', 'suspended', 'banned'])) {
            return back()->with('error', 'Invalid status.');
        }
        $user->update(['status' => $status]);
        $labels = ['active' => 'activated', 'suspended' => 'suspended', 'banned' => 'banned'];
        \App\Helpers\AuditHelper::log("Set user {$user->name} ({$user->email}) status to {$status}", 'user', $user->id);
        return back()->with('success', "User {$user->name} has been {$labels[$status]}.");
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        $user->update(['password' => Hash::make($request->new_password)]);
        return back()->with('success', "Password for {$user->name} updated successfully.");
    }

    public function walletAdjust(Request $request, User $user)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'type'        => 'required|in:fund,deduct',
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $amount = (float) $request->amount;

        if ($request->type === 'deduct' && $wallet->balance < $amount) {
            return back()->with('error', 'Insufficient wallet balance to deduct.');
        }

        if ($request->type === 'fund') {
            $wallet->increment('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }

        $txType = $request->type === 'fund' ? 'admin_credit' : 'admin_debit';
        WalletTransaction::create([
            'user_id'     => $user->id,
            'amount'      => $request->type === 'fund' ? $amount : -$amount,
            'type'        => $txType,
            'reference'   => 'ADMIN-' . strtoupper(uniqid()),
            'description' => $request->description ?: ($request->type === 'fund' ? 'Admin wallet funding' : 'Admin wallet deduction'),
        ]);

        \App\Helpers\AuditHelper::log(
            ($request->type === 'fund' ? 'Funded' : 'Deducted') . ' ₦' . number_format($amount, 2) . ' ' . ($request->type === 'fund' ? 'to' : 'from') . " {$user->name}'s wallet" . ($request->description ? ": {$request->description}" : ''),
            'user', $user->id
        );

        $action = $request->type === 'fund' ? 'funded' : 'deducted from';
        return back()->with('success', "₦" . number_format($amount, 2) . " {$action} {$user->name}'s wallet.");
    }

    public function impersonate(User $user)
    {
        session(['impersonate_user_id' => $user->id, 'impersonate_user_name' => $user->name]);
        return redirect()->route('admin.impersonate.dashboard', $user);
    }

    public function impersonateDashboard(User $user)
    {
        $wallet    = $user->wallet;
        $orders    = $user->purchases()->with('listing')->latest()->take(10)->get();
        $tickets   = $user->tickets()->latest()->take(5)->get();
        $wishlist  = $user->wishlists()->with('listing')->latest()->take(10)->get();
        $notifs    = $user->notifications()->latest()->take(10)->get();
        return view('admin.user-dashboard', compact('user', 'wallet', 'orders', 'tickets', 'wishlist', 'notifs'));
    }

    public function sendEmail(Request $request, User $user)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $siteName    = Setting::get('site_name', 'TopVerifi');
        $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
        $fromName    = Setting::get('mail_from_name', $siteName);

        try {
            $html = view('emails.admin-direct', [
                'user'        => $user,
                'subject'     => $request->subject,
                'messageBody' => $request->message,
                'siteName'    => $siteName,
            ])->render();

            Mail::html($html, function ($msg) use ($user, $request, $fromAddress, $fromName) {
                $msg->to($user->email, $user->name)
                    ->from($fromAddress, $fromName)
                    ->subject($request->subject);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }

        \App\Helpers\AuditHelper::log(
            "Sent email to {$user->name} ({$user->email}) — Subject: {$request->subject}",
            'user', $user->id
        );

        return back()->with('success', "Email sent to {$user->name} ({$user->email}).");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted.');
    }
}
