<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class PurchaseErrorLog extends Model
{
    protected $fillable = [
        'user_id', 'provider', 'action', 'error_message', 'context', 'ip_address',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper ─────────────────────────────────────────────────────────

    /**
     * Record a purchase error in the database.
     *
     * Call this wherever a user-facing purchase/cancel/order action fails so
     * admins can see a consolidated error feed without digging through log files.
     *
     * @param  string      $provider  herosms | grizzlysms | fivesim | jap | system
     * @param  string      $action    order | cancel | sms-check | boosting | balance
     * @param  string      $message   Human-readable error message shown to the user.
     * @param  array       $context   Extra details: country, service, order_id, etc.
     * @param  int|null    $userId    Authenticated user ID (null = unauthenticated).
     */
    public static function record(
        string $provider,
        string $action,
        string $message,
        array  $context = [],
        ?int   $userId  = null,
    ): void {
        try {
            static::create([
                'user_id'       => $userId,
                'provider'      => $provider,
                'action'        => $action,
                'error_message' => $message,
                'context'       => empty($context) ? null : $context,
                'ip_address'    => Request::ip(),
            ]);
        } catch (\Throwable) {
            // Logging must never crash the application — silently swallow DB errors.
        }
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * A short human-readable label for the provider column.
     */
    public function getProviderLabelAttribute(): string
    {
        return match ($this->provider) {
            'herosms'    => 'Hero-SMS',
            'grizzlysms' => 'GrizzlySMS',
            'fivesim'    => '5sim',
            'jap'        => 'SMM Boost',
            default      => ucfirst($this->provider),
        };
    }

    /**
     * Tailwind colour classes for the provider badge.
     */
    public function getProviderColorAttribute(): string
    {
        return match ($this->provider) {
            'herosms'    => 'bg-orange-500/20 text-orange-300',
            'grizzlysms' => 'bg-green-500/20 text-green-300',
            'fivesim'    => 'bg-blue-500/20 text-blue-300',
            'jap'        => 'bg-purple-500/20 text-purple-300',
            default      => 'bg-slate-500/20 text-slate-300',
        };
    }
}
