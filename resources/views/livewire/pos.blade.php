<div class="flex flex-col h-screen bg-gray-100 overflow-hidden" x-data="{ showCart: @entangle('showCart') }">
    <!-- Header -->
    <header class="bg-white border-b shadow-sm z-10 px-4 py-2 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="font-bold text-xl text-blue-600 hidden md:block">CoffeePOS</div>
            <div class="text-sm bg-gray-100 px-3 py-1 rounded-full text-gray-600">
                <span class="font-semibold">{{ auth()->user()->branch->name ?? 'Main Branch' }}</span>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-bold">{{ auth()->user()->name }}</div>
                <div class="text-xs text-gray-500 capitalize">{{ auth()->user()->getRoleNames()->first() }}</div>
            </div>

            <div class="flex space-x-2">
                <!-- Language Switcher -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button wire:click="changeLocale('id')" class="px-2 py-1 text-xs font-bold rounded {{ app()->getLocale() === 'id' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">ID</button>
                    <button wire:click="changeLocale('en')" class="px-2 py-1 text-xs font-bold rounded {{ app()->getLocale() === 'en' ? 'bg-white shadow text-blue-600' : 'text-gray-500' }}">EN</button>
                </div>

                <!-- Mobile Cart Toggle -->
                <button @click="showCart = !showCart" class="md:hidden bg-blue-100 text-blue-600 p-2 rounded-lg relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @if(count($cart) > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full">{{ count($cart) }}</span>
                    @endif
                </button>

                <!-- Logout -->
                <button wire:click="logout" class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition" title="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Main Content: Product List -->
        <main class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
            <!-- Filters -->
            <div class="p-4 bg-white border-b flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="{{ __('Search products...') }}" class="w-full pl-10 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <select wire:model.live="selectedCategory" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})" class="bg-white rounded-xl shadow-sm border border-gray-200 cursor-pointer hover:border-blue-500 hover:shadow-md transition-all overflow-hidden group">
                            <div class="relative pt-[100%] bg-gray-100 overflow-hidden">
                                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/300' }}"
                                     alt="{{ $product->name }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-sm text-gray-800 truncate mb-1">{{ $product->name }}</h3>
                                <div class="text-blue-600 font-bold text-sm">IDR {{ number_format($product->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="text-gray-400 mb-2 italic">{{ __('No products found') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- Right Side: Cart Sidebar (Desktop) -->
        <aside class="hidden md:flex w-80 lg:w-96 bg-white border-l flex-col shadow-xl">
            @include('livewire.pos-cart')
        </aside>

        <!-- Mobile Cart Sidebar (Overlay) -->
        <div x-show="showCart"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-0 z-50 md:hidden flex justify-end"
             style="display: none;">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black bg-opacity-50" @click="showCart = false"></div>

            <aside class="relative w-4/5 max-w-sm bg-white h-full flex flex-col shadow-2xl">
                <div class="p-4 border-b flex justify-between items-center bg-blue-600 text-white">
                    <span class="font-bold">{{ __('Current Order') }}</span>
                    <button @click="showCart = false" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
         x-on:notify.window="show = true; message = $event.detail[0].message; type = $event.detail[0].type; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-96 p-4 rounded-xl shadow-2xl text-white z-[100]"
         :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
         style="display: none;">
        <div class="flex items-center">
            <template x-if="type === 'success'">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
            <span x-text="message" class="font-semibold"></span>
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($lastSale)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col max-h-full">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <span class="font-bold">{{ __('Transaction Receipt') }}</span>
                <button wire:click="closeReceipt" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="receipt-content" class="flex-1 overflow-y-auto p-6 font-mono text-sm leading-tight bg-white">
                <div class="text-center mb-4">
                    <div class="font-bold text-lg">CoffeePOS</div>
                    <div>{{ $lastSale->branch->name }}</div>
                    <div class="text-xs">{{ $lastSale->branch->address }}</div>
                    <div class="text-xs">{{ __('Phone') }}: {{ $lastSale->branch->phone }}</div>
                </div>

                <div class="border-t border-dashed py-2 space-y-1">
                    <div class="flex justify-between">
                        <span>{{ __('Order #') }}</span>
                        <span>{{ $lastSale->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Date') }}</span>
                        <span>{{ $lastSale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Cashier') }}</span>
                        <span>{{ $lastSale->user->name }}</span>
                    </div>
                    @if($lastSale->customer_name)
                    <div class="flex justify-between">
                        <span>{{ __('Customer') }}</span>
                        <span>{{ $lastSale->customer_name }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-dashed py-2">
                    @foreach($lastSale->items as $item)
                    <div class="mb-2">
                        <div class="flex justify-between uppercase">
                            <span class="flex-1 mr-2">{{ $item->product->name }}</span>
                            <span>{{ $item->quantity }}x</span>
                            <span class="w-20 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-dashed py-2 space-y-1">
                    <div class="flex justify-between">
                        <span>{{ __('SUBTOTAL') }}</span>
                        <span>IDR {{ number_format($lastSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($lastSale->discount > 0)
                    <div class="flex justify-between text-red-500">
                        <span>{{ __('DISCOUNT') }}</span>
                        <span>-{{ number_format($lastSale->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-lg pt-1 border-t border-dashed">
                        <span>{{ __('TOTAL') }}</span>
                        <span>IDR {{ number_format($lastSale->final_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed py-2 space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span>{{ __('Payment Method') }}</span>
                        <span class="uppercase">{{ __($lastSale->payment_method) }}</span>
                    </div>
                    @if($lastSale->payment_method === 'cash')
                    <div class="flex justify-between">
                        <span>{{ __('Amount Received') }}</span>
                        <span>IDR {{ number_format($lastSale->received_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>{{ __('Change') }}</span>
                        <span>IDR {{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="text-center mt-6 text-xs italic">
                    {{ __('Thank you for your visit!') }}<br>
                    {{ __('Please come again.') }}
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t flex space-x-2">
                <button onclick="window.print()" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    {{ __('PRINT RECEIPT') }}
                </button>
            </div>
        </div>
    </div>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-content, #receipt-content * {
                visibility: visible;
            }
            #receipt-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                margin: 0;
            }
        }
    </style>
    @endif
</div>
