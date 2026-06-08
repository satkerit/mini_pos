<div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 via-indigo-50/30 to-cyan-50/20">
    {{-- Header --}}
    <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center space-x-3">
            <a href="/pos" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="font-bold text-slate-800 text-lg leading-tight">{{ __('Transaction History') }}</h1>
                <p class="text-xs text-slate-400">{{ __('Your recent transactions') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="changeLocale('en')" class="px-2 py-1 rounded-lg text-xs font-medium {{ $locale === 'en' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-400 hover:bg-slate-100' }}">EN</button>
            <button wire:click="changeLocale('id')" class="px-2 py-1 rounded-lg text-xs font-medium {{ $locale === 'id' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-400 hover:bg-slate-100' }}">ID</button>
            <button wire:click="logout" class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 hover:bg-rose-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </div>
    </header>

    {{-- Filters --}}
    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-slate-200/40">
        <div class="flex space-x-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-0 focus:outline-none transition-all"
                       placeholder="{{ __('Search order...') }}">
            </div>
            <input type="date" wire:model.live="filterDate"
                   class="px-3 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-0 focus:outline-none transition-all">
            <select wire:model.live="filterPaymentMethod"
                    class="px-3 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-0 focus:outline-none transition-all">
                <option value="">{{ __('All') }}</option>
                <option value="cash">{{ __('Cash') }}</option>
                <option value="qris">QRIS</option>
                <option value="e-wallet">{{ __('E-Wallet') }}</option>
                <option value="va">VA</option>
            </select>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin p-4">
        @if($sales->isEmpty())
        <div class="flex flex-col items-center justify-center pt-20">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-slate-400 font-medium">{{ __('No transactions found') }}</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($sales as $sale)
            <div wire:click="viewDetail({{ $sale->id }})"
                 class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 hover:shadow-md hover:border-indigo-200 transition-all cursor-pointer active:scale-[0.98]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br @if($sale->payment_method === 'cash') from-emerald-400 to-emerald-600 @elseif($sale->payment_method === 'qris') from-indigo-400 to-indigo-600 @elseif($sale->payment_method === 'e-wallet') from-sky-400 to-sky-600 @else from-amber-400 to-amber-600 @endif flex items-center justify-center shadow-sm">
                            <span class="text-white text-xs font-bold">#{{ substr($sale->order_number, -3) }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700">{{ $sale->order_number }}</div>
                            <div class="text-xs text-slate-400">
                                {{ $sale->customer_name ?? __('Walk-in') }}
                                &middot; {{ $sale->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-slate-800">Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-end space-x-1 mt-1">
                            <span class="badge @if($sale->payment_method === 'cash') bg-emerald-100 text-emerald-700 @elseif($sale->payment_method === 'qris') bg-indigo-100 text-indigo-700 @elseif($sale->payment_method === 'e-wallet') bg-sky-100 text-sky-700 @else bg-amber-100 text-amber-700 @endif text-[10px] px-2 py-0.5 rounded-full font-medium">
                                {{ strtoupper($sale->payment_method) }}
                            </span>
                            <span class="badge @if($sale->status === 'completed') bg-emerald-100 text-emerald-700 @elseif($sale->status === 'pending') bg-amber-100 text-amber-700 @else bg-rose-100 text-rose-700 @endif text-[10px] px-2 py-0.5 rounded-full font-medium">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetail && $selectedSale)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm" wire:click="closeDetail">
        <div class="bg-white w-full max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">{{ __('Transaction Detail') }}</h3>
                    <p class="text-xs text-slate-400">{{ $selectedSale->order_number }}</p>
                </div>
                <button wire:click="closeDetail" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                {{-- Info --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <div class="text-xs text-slate-400">{{ __('Date') }}</div>
                        <div class="text-sm font-semibold text-slate-700">{{ $selectedSale->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <div class="text-xs text-slate-400">{{ __('Cashier') }}</div>
                        <div class="text-sm font-semibold text-slate-700">{{ $selectedSale->user->name }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <div class="text-xs text-slate-400">{{ __('Customer') }}</div>
                        <div class="text-sm font-semibold text-slate-700">{{ $selectedSale->customer_name ?? __('Walk-in') }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <div class="text-xs text-slate-400">{{ __('Payment') }}</div>
                        <div class="text-sm font-semibold text-slate-700">{{ strtoupper($selectedSale->payment_method) }}</div>
                    </div>
                </div>

                {{-- Items --}}
                <div>
                    <h4 class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">{{ __('Items') }}</h4>
                    <div class="space-y-2">
                        @foreach($selectedSale->items as $item)
                        <div class="flex items-center justify-between bg-slate-50 rounded-xl p-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600">{{ $item->quantity }}x</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-700">{{ $item->product->name ?? 'Product' }}</div>
                                    <div class="text-xs text-slate-400">Rp {{ number_format($item->unit_price, 0, ',', '.') }} / pcs</div>
                                </div>
                            </div>
                            <div class="text-sm font-bold text-slate-800">Rp {{ number_format($item->total_price, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Totals --}}
                <div class="bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-2xl p-4 text-white space-y-2">
                    <div class="flex justify-between text-sm opacity-80">
                        <span>{{ __('Subtotal') }}</span>
                        <span>Rp {{ number_format($selectedSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedSale->discount > 0)
                    <div class="flex justify-between text-sm opacity-80">
                        <span>{{ __('Discount') }}</span>
                        <span>- Rp {{ number_format($selectedSale->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-white/20 pt-2 flex justify-between font-bold text-lg">
                        <span>{{ __('Total') }}</span>
                        <span>Rp {{ number_format($selectedSale->final_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedSale->payment_method === 'cash')
                    <div class="flex justify-between text-sm opacity-80">
                        <span>{{ __('Received') }}</span>
                        <span>Rp {{ number_format($selectedSale->received_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-medium">
                        <span>{{ __('Change') }}</span>
                        <span>Rp {{ number_format($selectedSale->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
