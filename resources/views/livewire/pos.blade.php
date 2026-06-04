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
                    <input type="text" wire:model.live="search" placeholder="Search products..." class="w-full pl-10 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <select wire:model.live="selectedCategory" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
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
                            <div class="text-gray-400 mb-2 italic">No products found</div>
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
                    <span class="font-bold">Current Order</span>
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
</div>
