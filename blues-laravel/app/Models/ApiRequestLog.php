<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_key_id', 'user_id', 'method', 'path',
        'status_code', 'ip', 'user_agent', 'response_ms', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }
}
