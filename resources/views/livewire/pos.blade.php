<div class="flex flex-col h-screen bg-slate-50 overflow-hidden"
     x-data="{ showCart: @entangle('showCart') }"
     x-init="$watch('showCart', val => { if (val) document.body.classList.add('overflow-hidden'); else document.body.classList.remove('overflow-hidden'); })">
    <!-- Header -->
    <header class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 text-white shadow-xl shadow-indigo-200/50 z-10 px-4 lg:px-6 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight hidden sm:block">CoffeePOS</span>
                </div>
                <div class="hidden md:flex items-center space-x-1.5 bg-white/10 backdrop-blur-sm rounded-xl px-3 py-1.5 text-xs text-white/80">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-semibold">{{ auth()->user()->branch->name ?? 'Main Branch' }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-semibold text-white/90">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-white/60 capitalize">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>

                <div class="flex items-center space-x-1.5">
                    <!-- Language Switcher -->
                    <div class="flex bg-white/10 backdrop-blur-sm rounded-xl p-0.5">
                        <button wire:click="changeLocale('id')" class="px-2.5 py-1.5 text-xs font-bold rounded-lg transition-all duration-200 {{ app()->getLocale() === 'id' ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">ID</button>
                        <button wire:click="changeLocale('en')" class="px-2.5 py-1.5 text-xs font-bold rounded-lg transition-all duration-200 {{ app()->getLocale() === 'en' ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">EN</button>
                    </div>

                    <!-- Mobile Cart Toggle -->
                    <button @click="showCart = !showCart" class="md:hidden relative bg-white/10 backdrop-blur-sm rounded-xl p-2.5 hover:bg-white/20 transition-all duration-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if(count($cart) > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-lg shadow-rose-300 animate-scale-in">{{ count($cart) }}</span>
                        @endif
                    </button>

                    <!-- Logout -->
                    <a href="/pos/shift" class="bg-white/10 backdrop-blur-sm rounded-xl p-2.5 hover:bg-white/20 transition-all duration-200" title="{{ __('Shift') }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    <a href="/pos/history" class="bg-white/10 backdrop-blur-sm rounded-xl p-2.5 hover:bg-white/20 transition-all duration-200" title="{{ __('History') }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    <button wire:click="logout" class="bg-white/10 backdrop-blur-sm rounded-xl p-2.5 hover:bg-white/20 transition-all duration-200" title="Logout">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-hidden">
            <!-- Filters -->
            <div class="p-4 lg:p-5 bg-white/80 backdrop-blur-sm border-b border-slate-100 flex flex-col sm:flex-row space-y-2.5 sm:space-y-0 sm:space-x-3">
                <div class="relative flex-1 group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 group-focus-within:text-indigo-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="{{ __('Search products by name, SKU, or barcode...') }}" class="w-full pl-10 input-modern">
                </div>
                <select wire:model.live="selectedCategory" class="input-modern sm:w-48">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-5 scrollbar-thin">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 lg:gap-4">
                    @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})"
                             class="group relative bg-white rounded-2xl border border-slate-100 cursor-pointer hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-100/30 transition-all duration-300 overflow-hidden animate-fade-in"
                             style="animation-delay: {{ $loop->index * 30 }}ms">
                            @php
                                $hues = [240, 200, 160, 300, 340, 20, 180];
                                $hue = $hues[$loop->index % count($hues)];
                            @endphp
                            <div class="relative pt-[90%] bg-gradient-to-br from-slate-50 overflow-hidden" style="--tw-gradient-stops: hsl({{ $hue }}, 30%, 95%), hsl({{ $hue }}, 20%, 98%)">
                                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/400x400/e2e8f0/94a3b8?text=' . urlencode(substr($product->name, 0, 1)) }}"
                                     alt="{{ $product->name }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/0 via-transparent to-transparent group-hover:from-black/10 transition-all duration-300"></div>
                                @if($product->is_active && $loop->first)
                                    <div class="absolute top-2 left-2 badge-success text-[10px] shadow-sm">New</div>
                                @endif
                            </div>
                            <div class="p-3 lg:p-3.5">
                                <h3 class="font-semibold text-sm text-slate-800 truncate mb-0.5 group-hover:text-indigo-600 transition-colors duration-200">{{ $product->name }}</h3>
                                <p class="text-[11px] text-slate-400 truncate mb-2">{{ $product->category?->name }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gradient">IDR {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center animate-fade-in">
                            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <p class="text-slate-400 font-medium">{{ __('No products found') }}</p>
                            <p class="text-sm text-slate-300 mt-1">{{ __('Try a different search or category') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- Cart Sidebar (Desktop) -->
        <aside class="hidden md:flex w-80 lg:w-[360px] bg-white border-l border-slate-100 flex-col shadow-[-4px_0_24px_-4px_rgba(0,0,0,0.05)]">
            @include('livewire.pos-cart')
        </aside>

        <!-- Mobile Cart Overlay -->
        <div x-show="showCart"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-0 z-50 md:hidden flex justify-end"
             style="display: none;">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showCart = false"></div>
            <aside class="relative w-[85%] max-w-sm bg-white h-full flex flex-col shadow-2xl">
                <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-indigo-600 to-violet-700 text-white">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="font-bold">{{ __('Current Order') }}</span>
                    </div>
                    <button @click="showCart = false" class="bg-white/10 rounded-lg p-1.5 hover:bg-white/20 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-hidden flex flex-col">
                    @include('livewire.pos-cart')
                </div>
            </aside>
        </div>
    </div>

    <!-- Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail[0].message; type = $event.detail[0].type; setTimeout(() => show = false, 3500)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-96 z-[100]"
         :class="type === 'success' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-200' : 'bg-gradient-to-r from-rose-500 to-rose-600 shadow-lg shadow-rose-200'"
         style="display: none;">
        <div class="flex items-center px-5 py-4 rounded-2xl text-white">
            <template x-if="type === 'success'">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mr-3 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </template>
            <template x-if="type === 'error'">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mr-3 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </template>
            <div>
                <p class="font-semibold text-sm" x-text="message"></p>
            </div>
            <button @click="show = false" class="ml-auto bg-white/10 rounded-lg p-1 hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($lastSale)
    <div class="modal-backdrop" x-data x-init="$el.classList.add('animate-fade-in')">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col max-h-[90vh] animate-scale-in">
            <div class="modal-header">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="font-bold text-slate-800">{{ __('Transaction Receipt') }}</span>
                </div>
                <button wire:click="closeReceipt" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="receipt-content" class="flex-1 overflow-y-auto p-6 font-mono text-sm leading-tight bg-white">
                <div class="text-center mb-5">
                    <div class="font-bold text-xl text-gradient">CoffeePOS</div>
                    <div class="text-slate-600 text-xs mt-0.5">{{ $lastSale->branch->name }}</div>
                    <div class="text-slate-400 text-[10px]">{{ $lastSale->branch->address }}</div>
                    <div class="text-slate-400 text-[10px]">{{ __('Phone') }}: {{ $lastSale->branch->phone }}</div>
                </div>

                <div class="border-t border-dashed border-slate-200 py-3 space-y-1.5 text-[11px]">
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Order #') }}</span>
                        <span class="font-semibold text-slate-800">{{ $lastSale->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Date') }}</span>
                        <span class="font-semibold text-slate-800">{{ $lastSale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Cashier') }}</span>
                        <span class="font-semibold text-slate-800">{{ $lastSale->user->name }}</span>
                    </div>
                    @if($lastSale->customer_name)
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Customer') }}</span>
                        <span class="font-semibold text-slate-800">{{ $lastSale->customer_name }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-dashed border-slate-200 py-3 space-y-2">
                    @foreach($lastSale->items as $item)
                    <div class="flex justify-between text-[11px]">
                        <div class="flex-1 min-w-0 mr-2">
                            <div class="font-semibold text-slate-800 truncate">{{ $item->product->name }}</div>
                            <div class="text-slate-400">{{ $item->quantity }}x IDR {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        </div>
                        <span class="font-semibold text-slate-800 w-20 text-right">IDR {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-dashed border-slate-200 py-3 space-y-1.5 text-[11px]">
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('SUBTOTAL') }}</span>
                        <span>IDR {{ number_format($lastSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($lastSale->discount > 0)
                    <div class="flex justify-between text-rose-500">
                        <span>{{ __('DISCOUNT') }}</span>
                        <span>-IDR {{ number_format($lastSale->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-base pt-2 border-t border-dashed border-slate-300 text-indigo-600">
                        <span>{{ __('TOTAL') }}</span>
                        <span>IDR {{ number_format($lastSale->final_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-200 py-3 space-y-1.5 text-[11px]">
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Payment Method') }}</span>
                        <span class="font-semibold uppercase text-slate-800">{{ __($lastSale->payment_method) }}</span>
                    </div>
                    @if($lastSale->payment_method === 'cash')
                    <div class="flex justify-between text-slate-600">
                        <span>{{ __('Amount Received') }}</span>
                        <span>IDR {{ number_format($lastSale->received_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-emerald-600">
                        <span>{{ __('Change') }}</span>
                        <span>IDR {{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="text-center mt-6 text-xs text-slate-400">
                    <p class="italic">{{ __('Thank you for your visit!') }}</p>
                    <p class="italic">{{ __('Please come again.') }}</p>
                </div>
            </div>

            <div class="modal-footer flex space-x-2.5">
                <button onclick="window.print()" class="btn-primary flex-1 text-xs py-3">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    {{ __('PRINT RECEIPT') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Cash Confirmation Modal -->
    @if($showConfirm)
    <div class="modal-backdrop">
        <div class="modal-content max-w-sm">
            <div class="modal-header">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="font-bold">{{ __('Confirm Cash Payment') }}</span>
                </div>
                <button wire:click="cancelCheckout" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body space-y-3">
                <div class="space-y-2">
                    @foreach($pendingItems as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 truncate mr-2">{{ $item['quantity']}}x {{ collect($cart)->firstWhere('id', $item['product_id'])['name'] ?? 'Item' }}</span>
                        <span class="font-semibold text-slate-800 whitespace-nowrap">IDR {{ number_format($item['total_price'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-dashed border-slate-200 pt-3 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">{{ __('Total') }}</span>
                        <span class="font-bold text-indigo-600">IDR {{ number_format($pendingSaleData['final_amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">{{ __('Received') }}</span>
                        <span class="font-semibold text-slate-800">IDR {{ number_format($pendingSaleData['received_amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-emerald-600 font-medium">{{ __('Change') }}</span>
                        <span class="font-bold text-emerald-600">IDR {{ number_format($pendingSaleData['change_amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex space-x-2.5 pt-2">
                    <button wire:click="cancelCheckout" class="flex-1 py-3 rounded-2xl border-2 border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="confirmSale" class="flex-[2] btn-success py-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Confirm Payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- QRIS Payment Modal -->
    @if($showQrPayment && $qrisTransaction)
    <div class="modal-backdrop">
        <div class="modal-content max-w-sm">
            <div class="modal-header">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <span class="font-bold">{{ __('QRIS Payment') }}</span>
                </div>
                <button wire:click="closeQrPayment" onclick="return confirm('{{ __('Are you sure? Payment will be cancelled and stock restored.') }}')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body text-center space-y-4">
                <div class="text-2xl font-bold text-gradient">IDR {{ number_format($qrisPayment->amount ?? 0, 0, ',', '.') }}</div>
                <div class="bg-white p-4 rounded-2xl inline-block border-2 border-dashed border-slate-200 shadow-sm">
                    <img src="{{ route('qris.image', $qrisTransaction->transaction_id) }}"
                         alt="QRIS QR Code"
                         class="w-48 h-48 mx-auto">
                </div>
                <p class="text-xs text-slate-500">{{ __('Scan this QR code with your e-wallet app') }}</p>
                <div class="inline-flex items-center space-x-1.5 bg-slate-50 rounded-xl px-3 py-1.5 font-mono text-xs text-slate-400">
                    <span>{{ $qrisTransaction->transaction_id }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $qrisTransaction->transaction_id }}')" class="text-indigo-500 hover:text-indigo-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    </button>
                </div>
                <div class="flex space-x-2.5">
                    <button wire:click="closeQrPayment" onclick="return confirm('{{ __('Cancel payment? Stock will be restored.') }}')" class="flex-1 py-3 rounded-2xl border-2 border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="checkQrisStatus({{ $qrisPayment->id }})" class="btn-success flex-[2] py-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Check Payment Status') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- E-Wallet Payment Modal -->
    @if($showEwalletPayment && $ewalletPayment)
    <div class="modal-backdrop">
        <div class="modal-content max-w-sm">
            <div class="modal-header">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="font-bold">{{ __('E-Wallet Payment') }}</span>
                </div>
                <button wire:click="closeEwalletPayment" onclick="return confirm('{{ __('Are you sure? Payment will be cancelled and stock restored.') }}')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body text-center space-y-4">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-sky-400 to-cyan-500 rounded-2xl shadow-lg shadow-sky-200 flex items-center justify-center animate-bounce-gentle">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-800">{{ __('E-Wallet Payment') }}</div>
                    <div class="text-2xl font-bold text-gradient mt-1">IDR {{ number_format($ewalletPayment->amount ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="bg-sky-50 rounded-2xl p-4 border border-sky-100">
                    <div class="flex items-center justify-center space-x-2 text-sm text-sky-700">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="font-medium">{{ __('Redirecting to e-wallet app...') }}</span>
                    </div>
                </div>
                <div class="flex space-x-2.5">
                    <button wire:click="closeEwalletPayment" onclick="return confirm('{{ __('Cancel payment? Stock will be restored.') }}')" class="flex-1 py-3 rounded-2xl border-2 border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="confirmEwallet({{ $ewalletPayment->id }})" class="flex-[2] btn-success py-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Payment Successful') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Virtual Account Payment Modal -->
    @if($showVaPayment && $vaPayment)
    <div class="modal-backdrop">
        <div class="modal-content max-w-sm">
            <div class="modal-header">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span class="font-bold">{{ __('Virtual Account') }}</span>
                </div>
                <button wire:click="closeVaPayment" onclick="return confirm('{{ __('Are you sure? Payment will be cancelled and stock restored.') }}')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body text-center space-y-4">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg shadow-amber-200 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-800">{{ __('Bank Transfer') }}</div>
                    <div class="text-2xl font-bold text-gradient mt-1">IDR {{ number_format($vaPayment->amount ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 space-y-2">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ __('Virtual Account Number') }}</div>
                    <div class="text-2xl font-bold font-mono tracking-[0.2em] text-indigo-600 select-all">{{ $vaNumber }}</div>
                    <div class="flex justify-center space-x-1.5">
                        <span class="badge bg-slate-100 text-slate-500 text-[10px]">BCA</span>
                        <span class="badge bg-slate-100 text-slate-500 text-[10px]">Mandiri</span>
                        <span class="badge bg-slate-100 text-slate-500 text-[10px]">BNI</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500">{{ __('Transfer the amount to the virtual account above') }}</p>
                <div class="flex space-x-2.5">
                    <button wire:click="closeVaPayment" onclick="return confirm('{{ __('Cancel payment? Stock will be restored.') }}')" class="flex-1 py-3 rounded-2xl border-2 border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="confirmVa({{ $vaPayment->id }})" class="flex-[2] btn-success py-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Payment Received') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        @media print {
            body * { visibility: hidden; }
            #receipt-content, #receipt-content * { visibility: visible; }
            #receipt-content {
                position: absolute; left: 0; top: 0; width: 100%;
                padding: 0; margin: 0;
            }
            #receipt-content .text-gradient {
                background: none !important;
                color: #1e293b !important;
            }
        }
    </style>
    @if($showQrPayment || $showEwalletPayment || $showVaPayment || $lastSale)
    @endif
</div>
