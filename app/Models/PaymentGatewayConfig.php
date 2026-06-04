<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_code',
        'gateway_name',
        'merchant_id',
        'merchant_name',
        'api_key',
        'api_secret',
        'api_endpoint',
        'callback_url',
        'qr_merchant_id',
        'qr_merchant_key',
        'is_active',
        'is_sandbox',
        'extra_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'extra_config' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentGatewayLog::class);
    }
}
