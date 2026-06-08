<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashShift extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'total_cash_sales' => 'decimal:2',
            'total_non_cash_sales' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('opened_at', now()->toDateString());
    }

    public function recalculateTotals(): void
    {
        $this->load('sales');

        $this->total_cash_sales = $this->sales
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('final_amount');

        $this->total_non_cash_sales = $this->sales
            ->where('status', 'completed')
            ->where('payment_method', '!=', 'cash')
            ->sum('final_amount');

        $this->total_sales = $this->sales
            ->where('status', 'completed')
            ->sum('final_amount');

        $this->expected_cash = $this->opening_balance + $this->total_cash_sales;

        $this->save();
    }
}
