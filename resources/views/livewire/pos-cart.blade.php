<div class="flex flex-col h-full bg-white">
    <!-- Cart Header -->
    <div class="px-5 pt-5 pb-2 border-b border-slate-50">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="font-bold text-slate-800 text-sm">{{ __('Order Items') }}</span>
                <span class="badge-primary text-[10px]">{{ count($cart) }}</span>
            </div>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('Customer Name') }}</label>
            <input type="text" wire:model.live="customerName" placeholder="{{ __('Optional') }}" class="input-modern text-xs py-2">
        </div>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto px-5 py-3 space-y-2 scrollbar-thin">
        @forelse($cart as $id => $item)
            <div class="flex items-start justify-between bg-slate-50 p-3 rounded-2xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all duration-200 group animate-fade-in">
                <div class="flex-1 min-w-0 mr-2">
                    <div class="font-semibold text-sm text-slate-800 truncate">{{ $item['name'] }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">IDR {{ number_format($item['price'], 0, ',', '.') }}</div>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <div class="flex items-center bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="px-2.5 py-1.5 hover:bg-rose-50 text-slate-500 hover:text-rose-600 border-r border-slate-200 transition-colors duration-150">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                        </button>
                        <span class="px-3 py-1.5 text-xs font-bold w-9 text-center text-slate-700">{{ $item['quantity'] }}</span>
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="px-2.5 py-1.5 hover:bg-emerald-50 text-slate-500 hover:text-emerald-600 border-l border-slate-200 transition-colors duration-150">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <div class="font-bold text-xs text-gradient">
                        IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-[60%] text-slate-300 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-slate-400">{{ __('Cart is empty') }}</p>
                    <p class="text-xs text-slate-300 mt-1">{{ __('Click products to add them') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Summary & Checkout -->
    <div class="px-5 py-4 bg-white border-t border-slate-100 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.05)] space-y-4">
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">{{ __('Subtotal') }}</span>
                <span class="font-semibold text-slate-700">IDR {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-rose-500 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Discount') }}
                </span>
                <div class="flex items-center border border-slate-200 rounded-xl px-3 py-1.5 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-300 bg-slate-50/50">
                    <span class="text-xs text-slate-400 mr-1 font-medium">IDR</span>
                    <input type="number" wire:model.live="discount" class="w-20 text-right border-none p-0 focus:ring-0 text-sm font-semibold text-rose-500 bg-transparent" placeholder="0">
                </div>
            </div>
            <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                <span class="font-bold text-slate-800">{{ __('Total') }}</span>
                <span class="text-lg font-bold text-gradient">IDR {{ number_format($finalTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-2.5">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ __('Payment Method') }}</label>
            <div class="grid grid-cols-2 gap-2">
                <button wire:click="$set('paymentMethod', 'cash')"
                        class="flex flex-col items-center py-2.5 px-2 rounded-2xl border-2 transition-all duration-200 {{ $paymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm shadow-emerald-100' : 'border-slate-100 bg-white text-slate-500 hover:border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 mb-1 {{ $paymentMethod === 'cash' ? 'text-emerald-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('CASH') }}</span>
                </button>
                <button wire:click="$set('paymentMethod', 'qris')"
                        class="flex flex-col items-center py-2.5 px-2 rounded-2xl border-2 transition-all duration-200 {{ $paymentMethod === 'qris' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100' : 'border-slate-100 bg-white text-slate-500 hover:border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 mb-1 {{ $paymentMethod === 'qris' ? 'text-indigo-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('QRIS') }}</span>
                </button>
                <button wire:click="$set('paymentMethod', 'e-wallet')"
                        class="flex flex-col items-center py-2.5 px-2 rounded-2xl border-2 transition-all duration-200 {{ $paymentMethod === 'e-wallet' ? 'border-sky-500 bg-sky-50 text-sky-700 shadow-sm shadow-sky-100' : 'border-slate-100 bg-white text-slate-500 hover:border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 mb-1 {{ $paymentMethod === 'e-wallet' ? 'text-sky-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('E-WALLET') }}</span>
                </button>
                <button wire:click="$set('paymentMethod', 'va')"
                        class="flex flex-col items-center py-2.5 px-2 rounded-2xl border-2 transition-all duration-200 {{ $paymentMethod === 'va' ? 'border-amber-500 bg-amber-50 text-amber-700 shadow-sm shadow-amber-100' : 'border-slate-100 bg-white text-slate-500 hover:border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 mb-1 {{ $paymentMethod === 'va' ? 'text-amber-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span class="text-[10px] font-bold">{{ __('V.A.') }}</span>
                </button>
            </div>
        </div>

        @if($paymentMethod === 'cash')
        <div class="space-y-2.5 bg-slate-50 rounded-2xl p-4 border border-slate-100">
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-600 font-semibold text-xs uppercase tracking-wider">{{ __('Cash Received') }}</span>
                <div class="flex items-center border border-slate-200 rounded-xl px-3 py-1.5 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-300 bg-white">
                    <span class="text-xs text-slate-400 mr-1 font-medium">IDR</span>
                    <input type="number" wire:model.live="receivedAmount" class="w-24 text-right border-none p-0 focus:ring-0 text-sm font-bold text-slate-800 bg-transparent" placeholder="0">
                </div>
            </div>
            <div class="flex justify-between items-center text-sm pt-1">
                <span class="text-slate-600 font-semibold text-xs uppercase tracking-wider">{{ __('Change') }}</span>
                <span class="font-bold text-lg {{ $changeAmount > 0 ? 'text-emerald-600' : 'text-slate-400' }}">IDR {{ number_format($changeAmount, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <button wire:click="checkout"
                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none text-sm"
                @if(empty($cart) || ($paymentMethod === 'cash' && ($receivedAmount === '' || (float)$receivedAmount < (float)$finalTotal))) disabled @endif>
            <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('CHECKOUT') }} — IDR {{ number_format($finalTotal, 0, ',', '.') }}
            </span>
        </button>
    </div>
</div>
