<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class HistoryPage extends Component
{
    public $locale;
    public $branchId;
    public $search = '';
    public $selectedSale = null;
    public $showDetail = false;
    public $filterDate = '';
    public $filterPaymentMethod = '';

    public function mount()
    {
        $user = Auth::user();
        $this->branchId = $user->branch_id;
        $this->locale = $user->locale ?? app()->getLocale();
        $this->filterDate = now()->format('Y-m-d');
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

    public function viewDetail($saleId)
    {
        $this->selectedSale = Sale::with(['items.product', 'payments.paymentMethod', 'branch', 'user'])
            ->where('branch_id', $this->branchId)
            ->find($saleId);

        if ($this->selectedSale) {
            $this->showDetail = true;
        }
    }

    public function closeDetail()
    {
        $this->selectedSale = null;
        $this->showDetail = false;
    }

    public function getSalesProperty()
    {
        $query = Sale::where('branch_id', $this->branchId)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payments'])
            ->orderBy('created_at', 'desc');

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }

        if ($this->filterPaymentMethod) {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query->limit(100)->get();
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
        return view('livewire.history-page', [
            'sales' => $this->sales,
        ])->layout('layouts.pos');
    }
}
