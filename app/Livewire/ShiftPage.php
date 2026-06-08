<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashShift;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class ShiftPage extends Component
{
    public $branchId;
    public $locale;
    public $openingBalance = '';
    public $closingBalance = '';
    public $actualCash = '';
    public $notes = '';
    public $currentShift = null;
    public $todaySales = [];
    public $todaySummary = [];

    public function mount()
    {
        $user = Auth::user();
        $this->branchId = $user->branch_id;
        $this->locale = $user->locale ?? app()->getLocale();
        $this->loadCurrentShift();
    }

    public function changeLocale($lang)
    {
        if (in_array($lang, ['en', 'id'])) {
            $this->locale = $lang;
            if (auth()->check()) {
                auth()->user()->update(['locale' => $lang]);
            }
            session(['locale' => $lang]);
            app()->setLocale($lang);
            return redirect(request()->header('Referer'));
        }
    }

    public function loadCurrentShift()
    {
        $this->currentShift = CashShift::where('branch_id', $this->branchId)
            ->where('user_id', Auth::id())
            ->open()
            ->latest('opened_at')
            ->first();

        if ($this->currentShift) {
            $this->loadTodaySales();
        }
    }

    public function loadTodaySales()
    {
        if (!$this->currentShift) return;

        $this->todaySales = Sale::where('cash_shift_id', $this->currentShift->id)
            ->with(['items.product', 'payments'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $this->currentShift->recalculateTotals();
        $this->currentShift->refresh();

        $this->todaySummary = [
            'total_sales' => $this->currentShift->total_sales,
            'cash_sales' => $this->currentShift->total_cash_sales,
            'non_cash_sales' => $this->currentShift->total_non_cash_sales,
            'opening_balance' => $this->currentShift->opening_balance,
            'expected_cash' => $this->currentShift->expected_cash,
            'transaction_count' => count($this->todaySales),
        ];
    }

    public function openShift()
    {
        $balance = (float) $this->openingBalance;
        if ($balance < 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('Opening balance cannot be negative')]);
            return;
        }

        $existingOpen = CashShift::where('branch_id', $this->branchId)
            ->where('user_id', Auth::id())
            ->open()
            ->exists();

        if ($existingOpen) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('You already have an open shift')]);
            return;
        }

        $this->currentShift = CashShift::create([
            'branch_id' => $this->branchId,
            'user_id' => Auth::id(),
            'opening_balance' => $balance,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->openingBalance = '';
        $this->dispatch('notify', ['type' => 'success', 'message' => __('Shift opened successfully!')]);
    }

    public function closeShift()
    {
        if (!$this->currentShift) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('No open shift found')]);
            return;
        }

        $actual = (float) $this->actualCash;
        $this->currentShift->recalculateTotals();
        $this->currentShift->refresh();

        $this->currentShift->update([
            'closing_balance' => $actual,
            'actual_cash' => $actual,
            'difference' => $actual - $this->currentShift->expected_cash,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $this->notes ?: null,
        ]);

        $diff = $this->currentShift->difference;
        $msg = $diff == 0
            ? __('Shift closed. Cash matches perfectly!')
            : __('Shift closed. Difference: :amount', ['amount' => number_format($diff, 0, ',', '.')]);

        $this->currentShift = null;
        $this->todaySales = [];
        $this->todaySummary = [];
        $this->closingBalance = '';
        $this->actualCash = '';
        $this->notes = '';

        $this->dispatch('notify', ['type' => $diff == 0 ? 'success' : 'warning', 'message' => $msg]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.shift-page')->layout('layouts.pos');
    }
}
