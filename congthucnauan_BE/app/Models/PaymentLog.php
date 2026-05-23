<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'transaction_id',
        'action',
        'gateway',
        'request_data',
        'response_data',
        'status',
        'message',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'response_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the payment that owns the log.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope a query to filter by gateway.
     */
    public function scopeGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}