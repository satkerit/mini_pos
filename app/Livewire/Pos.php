<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Sale;
use App\Services\SalesService;
use Illuminate\Support\Facades\Auth;

class Pos extends Component
{
    public $search = '';
    public $selectedCategory = null;
    public $cart = [];
    public $total = 0;
    public $discount = 0;
    public $finalTotal = 0;
    public $branch_id;
    public $paymentMethod = 'cash';
    public $showCart = false;
    public $lastSale = null;
    public $customerName = '';
    public $receivedAmount = 0;
    public $changeAmount = 0;
    public $locale;

    public function mount()
    {
        $this->branch_id = Auth::user()->branch_id ?? Branch::first()?->id;
        $this->locale = auth()->user()->locale ?? app()->getLocale();
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

    public function updatedReceivedAmount()
    {
        $this->calculateChange();
    }

    public function updatedDiscount()
    {
        $this->calculateTotal();
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->changeAmount = max(0, $this->receivedAmount - $this->finalTotal);
    }

    public function closeReceipt()
    {
        $this->lastSale = null;
        $this->customerName = '';
        $this->receivedAmount = 0;
        $this->changeAmount = 0;
    }

    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        $this->calculateTotal();

        // Auto-add if search matches barcode exactly
        if ($this->search === $product->barcode || $this->search === $product->sku) {
            $this->search = '';
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
        } else {
            $this->cart[$productId]['quantity'] = $quantity;
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->finalTotal = $this->total - $this->discount;
    }

    public function checkout(SalesService $salesService, \App\Services\PaymentService $paymentService)
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Cart is empty']);
            return;
        }

        $items = [];
        foreach ($this->cart as $item) {
            $items[] = [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
            ];
        }

        $sale = $salesService->createSale([
            'branch_id' => $this->branch_id,
            'user_id' => Auth::id(),
            'customer_name' => $this->customerName,
            'total_amount' => $this->total,
            'discount' => $this->discount,
            'final_amount' => $this->finalTotal,
            'payment_method' => $this->paymentMethod,
            'received_amount' => $this->receivedAmount,
            'change_amount' => $this->changeAmount,
            'items' => $items,
        ]);

        $paymentService->processPayment($sale->id, $this->finalTotal, $this->paymentMethod);

        $this->lastSale = Sale::with('items.product', 'branch', 'user')->find($sale->id);

        $this->cart = [];
        $this->total = 0;
        $this->discount = 0;
        $this->finalTotal = 0;
        $this->showCart = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Sale completed successfully!']);
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', $this->search)
                    ->orWhere('barcode', $this->search);
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->where('is_active', true)
            ->get();

        $categories = Category::all();

        return view('livewire.pos', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('layouts.pos');
    }
}
