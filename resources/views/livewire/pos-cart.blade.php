<div class="flex flex-col h-full bg-white">
    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ __('Customer Name') }}</label>
            <input type="text" wire:model.live="customerName" placeholder="{{ __('Optional') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        @forelse($cart as $id => $item)
            <div class="flex items-start justify-between bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex-1 min-w-0 mr-2">
                    <div class="font-bold text-sm text-gray-800 truncate">{{ $item['name'] }}</div>
                    <div class="text-xs text-gray-500">IDR {{ number_format($item['price'], 0, ',', '.') }}</div>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <div class="flex items-center bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="px-2 py-1 hover:bg-gray-100 text-gray-600 border-r border-gray-200">-</button>
                        <span class="px-3 py-1 text-xs font-bold w-10 text-center">{{ $item['quantity'] }}</span>
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="px-2 py-1 hover:bg-gray-100 text-gray-600 border-l border-gray-200">+</button>
                    </div>
                    <div class="font-bold text-sm text-blue-600">
                        IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-2">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <p class="text-sm italic">{{ __('Cart is empty') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Summary & Checkout -->
    <div class="p-4 bg-white border-t shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] space-y-4">
        <div class="space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ __('Subtotal') }}</span>
                <span>IDR {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-red-500">{{ __('Discount') }}</span>
                <div class="flex items-center border border-gray-300 rounded-lg px-2 py-1 focus-within:ring-1 focus-within:ring-blue-500">
                    <span class="text-xs text-gray-400 mr-1">IDR</span>
                    <input type="number" wire:model.live="discount" class="w-20 text-right border-none p-0 focus:ring-0 text-sm font-semibold text-red-500" placeholder="0">
                </div>
            </div>
            <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-2">
                <span>{{ __('Total') }}</span>
                <span class="text-blue-600">IDR {{ number_format($finalTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Payment Method') }}</label>
            <div class="grid grid-cols-2 gap-2">
                <button wire:click="$set('paymentMethod', 'cash')"
                        class="flex flex-col items-center py-2 px-1 rounded-xl border-2 transition-all {{ $paymentMethod === 'cash' ? 'bg-blue-50 border-blue-600 text-blue-600' : 'bg-white border-gray-100 text-gray-500 hover:border-gray-200' }}">
                    <svg class="h-5 w-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('CASH') }}</span>
                </button>
                <button wire:click="$set('paymentMethod', 'qris')" 
                        class="flex flex-col items-center py-2 px-1 rounded-xl border-2 transition-all {{ $paymentMethod === 'qris' ? 'bg-blue-50 border-blue-600 text-blue-600' : 'bg-white border-gray-100 text-gray-500 hover:border-gray-200' }}">
                    <svg class="h-5 w-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('QRIS') }}</span>
                </button>
            </div>
        </div>

        @if($paymentMethod === 'cash')
        <div class="space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 font-bold uppercase text-xs">{{ __('Cash Received') }}</span>
                <div class="flex items-center border border-gray-300 rounded-lg px-2 py-1 focus-within:ring-1 focus-within:ring-blue-500">
                    <span class="text-xs text-gray-400 mr-1">IDR</span>
                    <input type="number" wire:model.live="receivedAmount" class="w-24 text-right border-none p-0 focus:ring-0 text-sm font-bold text-gray-900" placeholder="0">
                </div>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 font-bold uppercase text-xs">{{ __('Change') }}</span>
                <span class="font-bold text-orange-600">IDR {{ number_format($changeAmount, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <button wire:click="checkout" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98] disabled:bg-gray-300 disabled:shadow-none"
                @if(empty($cart) || ($paymentMethod === 'cash' && $receivedAmount < $finalTotal)) disabled @endif>
            {{ __('CHECKOUT') }}
        </button>
    </div>
</div>
