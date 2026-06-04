<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGatewayLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_gateway_config_id',
        'payment_id',
        'endpoint',
        'method',
        'request_body',
        'response_body',
        'response_code',
        'status',
        'error_message',
        'ip_address',
    ];

    public function config(): BelongsTo
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_gateway_config_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
