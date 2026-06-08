<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\Payment;
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
    public $receivedAmount = '';
    public $changeAmount = 0;
    public $locale;

    public $showConfirm = false;
    public $pendingItems = [];
    public $pendingSaleData = [];

    public $showQrPayment = false;
    public $qrisTransaction = null;
    public $qrisPayment = null;

    public $showEwalletPayment = false;
    public $ewalletPayment = null;

    public $showVaPayment = false;
    public $vaPayment = null;
    public $vaNumber = '';

    public function mount()
    {
        $user = Auth::user();
        $this->branch_id = $user->branch_id ?? cache()->remember('first_branch_id', 3600, fn() => Branch::first()?->id);
        $this->locale = $user->locale ?? app()->getLocale();
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
        $received = (float) $this->receivedAmount;
        $this->changeAmount = max(0, $received - $this->finalTotal);
    }

    public function closeReceipt()
    {
        $this->lastSale = null;
        $this->customerName = '';
        $this->receivedAmount = '';
        $this->changeAmount = 0;
    }

    public function cancelCheckout()
    {
        $this->showConfirm = false;
        $this->showQrPayment = false;
        $this->showEwalletPayment = false;
        $this->showVaPayment = false;
        $this->pendingItems = [];
        $this->pendingSaleData = [];
        $this->qrisPayment = null;
        $this->qrisTransaction = null;
        $this->ewalletPayment = null;
        $this->vaPayment = null;
        $this->vaNumber = '';
        $this->dispatch('notify', ['type' => 'error', 'message' => __('Transaction cancelled.')]);
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
        $product = Product::select(['id', 'name', 'price', 'sku', 'barcode'])->find($productId);
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

        if ($this->paymentMethod === 'cash') {
            $received = (float) $this->receivedAmount;
            if ($received <= 0) {
                $this->dispatch('notify', ['type' => 'error', 'message' => __('Please enter the amount received')]);
                return;
            }
            if ($received < $this->finalTotal) {
                $this->dispatch('notify', ['type' => 'error', 'message' => __('Amount received is less than total')]);
                return;
            }

            $this->pendingItems = [];
            foreach ($this->cart as $item) {
                $this->pendingItems[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ];
            }

            $this->pendingSaleData = [
                'branch_id' => $this->branch_id,
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName,
                'total_amount' => $this->total,
                'discount' => $this->discount,
                'final_amount' => $this->finalTotal,
                'payment_method' => $this->paymentMethod,
                'received_amount' => $received,
                'change_amount' => $this->changeAmount,
            ];

            $this->showConfirm = true;
            $this->showCart = false;
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
            'received_amount' => $this->finalTotal,
            'change_amount' => 0,
            'items' => $items,
        ]);

        $payment = $paymentService->processPayment($sale->id, $this->finalTotal, $this->paymentMethod);

        $this->cart = [];
        $this->total = 0;
        $this->discount = 0;
        $this->finalTotal = 0;
        $this->receivedAmount = '';
        $this->changeAmount = 0;
        $this->showCart = false;

        if ($this->paymentMethod === 'qris') {
            $payment->load('qrisTransaction');
            $this->qrisPayment = $payment;
            $this->qrisTransaction = $payment->qrisTransaction;
            $this->showQrPayment = true;
        } elseif ($this->paymentMethod === 'e-wallet') {
            $this->ewalletPayment = $payment;
            $this->showEwalletPayment = true;
        } elseif ($this->paymentMethod === 'va') {
            $this->vaPayment = $payment;
            $this->vaNumber = '888' . str_pad($sale->id, 8, '0', STR_PAD_LEFT);
            $this->showVaPayment = true;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => __('Waiting for payment...')]);
    }

    protected function generateTempTxId(): string
    {
        return 'TXN' . strtoupper(uniqid());
    }

    public function confirmSale(SalesService $salesService, \App\Services\PaymentService $paymentService)
    {
        if (empty($this->pendingItems)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No pending transaction']);
            return;
        }

        $sale = $salesService->createSale(array_merge($this->pendingSaleData, [
            'items' => $this->pendingItems,
        ]));

        $paymentService->processPayment($sale->id, $this->pendingSaleData['final_amount'], $this->pendingSaleData['payment_method']);

        $this->lastSale = Sale::with('items.product', 'branch', 'user')->find($sale->id);

        $this->cart = [];
        $this->total = 0;
        $this->discount = 0;
        $this->finalTotal = 0;
        $this->pendingItems = [];
        $this->pendingSaleData = [];
        $this->receivedAmount = '';
        $this->changeAmount = 0;
        $this->showConfirm = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => __('Sale completed successfully!')]);
    }

    public function closeQrPayment(SalesService $salesService)
    {
        if ($this->qrisPayment) {
            $sale = Sale::find($this->qrisPayment->sale_id);
            if ($sale && $sale->status === 'pending') {
                $salesService->cancelSale($sale);
                $this->dispatch('notify', ['type' => 'error', 'message' => __('Payment cancelled. Stock restored.')]);
            }
        }
        $this->showQrPayment = false;
        $this->qrisTransaction = null;
        $this->qrisPayment = null;
    }

    public function closeEwalletPayment(SalesService $salesService)
    {
        if ($this->ewalletPayment) {
            $sale = Sale::find($this->ewalletPayment->sale_id);
            if ($sale && $sale->status === 'pending') {
                $salesService->cancelSale($sale);
                $this->dispatch('notify', ['type' => 'error', 'message' => __('Payment cancelled. Stock restored.')]);
            }
        }
        $this->showEwalletPayment = false;
        $this->ewalletPayment = null;
    }

    public function closeVaPayment(SalesService $salesService)
    {
        if ($this->vaPayment) {
            $sale = Sale::find($this->vaPayment->sale_id);
            if ($sale && $sale->status === 'pending') {
                $salesService->cancelSale($sale);
                $this->dispatch('notify', ['type' => 'error', 'message' => __('Payment cancelled. Stock restored.')]);
            }
        }
        $this->showVaPayment = false;
        $this->vaPayment = null;
        $this->vaNumber = '';
    }

    public function checkQrisStatus($paymentId, SalesService $salesService, \App\Services\PaymentService $paymentService)
    {
        $payment = Payment::find($paymentId);
        if (!$payment) return;

        $result = $paymentService->checkPaymentStatus($payment);

        if ($result['success'] && $result['status'] === 'completed') {
            $salesService->completeSale($payment->sale);

            $this->lastSale = Sale::with('items.product', 'branch', 'user')->find($payment->sale_id);
            $this->showQrPayment = false;

            $this->dispatch('notify', ['type' => 'success', 'message' => __('Payment confirmed!')]);
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('Payment not yet confirmed. Please scan the QR code.')]);
        }
    }

    public function confirmEwallet($paymentId, SalesService $salesService)
    {
        $payment = Payment::find($paymentId);
        if (!$payment) return;

        $salesService->completeSale($payment->sale);

        $this->lastSale = Sale::with('items.product', 'branch', 'user')->find($payment->sale_id);
        $this->showEwalletPayment = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => __('Payment confirmed!')]);
    }

    public function confirmVa($paymentId, SalesService $salesService)
    {
        $payment = Payment::find($paymentId);
        if (!$payment) return;

        $salesService->completeSale($payment->sale);

        $this->lastSale = Sale::with('items.product', 'branch', 'user')->find($payment->sale_id);
        $this->showVaPayment = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => __('Payment confirmed!')]);
    }

    public function render()
    {
        $products = Product::query()
            ->select(['id', 'name', 'price', 'category_id', 'image', 'sku', 'barcode'])
            ->with('category:id,name')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', $this->search)
                        ->orWhere('barcode', $this->search);
                });
            })
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->where('is_active', true)
            ->limit(200)
            ->get();

        $categories = Category::select(['id', 'name'])->get();

        return view('livewire.pos', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('layouts.pos');
    }
}
