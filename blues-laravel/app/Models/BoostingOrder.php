<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostingOrder extends Model
{
    protected $fillable = [
        'user_id',
        'jap_order_id',
        'service_id',
        'service_name',
        'category',
        'link',
        'quantity',
        'charge',
        'start_count',
        'remains',
        'status',
    ];

    protected $casts = [
        'charge' => 'decimal:2',
        'quantity' => 'integer',
        'start_count' => 'integer',
        'remains' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'text-green-400',
            'in_progress', 'processing' => 'text-blue-400',
            'pending'   => 'text-yellow-400',
            'partial'   => 'text-orange-400',
            'cancelled', 'canceled' => 'text-red-400',
            default     => 'text-slate-400',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed'  => 'bg-green-900/40 text-green-300 border-green-700',
            'in_progress', 'processing' => 'bg-blue-900/40 text-blue-300 border-blue-700',
            'pending'    => 'bg-yellow-900/40 text-yellow-300 border-yellow-700',
            'partial'    => 'bg-orange-900/40 text-orange-300 border-orange-700',
            'cancelled', 'canceled' => 'bg-red-900/40 text-red-300 border-red-700',
            default      => 'bg-slate-700/40 text-slate-300 border-slate-600',
        };
    }
}
