<div class="flex h-screen bg-gray-100">
    <!-- Left Side: Product List -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <!-- Search and Category -->
        <div class="p-4 bg-white border-b flex space-x-4">
            <input type="text" wire:model.live="search" placeholder="Search products..." class="flex-1 border rounded-lg px-4 py-2">
            <select wire:model.live="selectedCategory" class="border rounded-lg px-4 py-2">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
                <div wire:click="addToCart({{ $product->id }})" class="bg-white rounded-lg shadow cursor-pointer hover:shadow-lg transition overflow-hidden border-2 border-transparent hover:border-blue-500">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/150' }}" alt="{{ $product->name }}" class="w-full h-32 object-cover">
                    <div class="p-2">
                        <div class="font-bold truncate">{{ $product->name }}</div>
                        <div class="text-blue-600 font-bold">IDR {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Side: Cart -->
    <div class="w-96 bg-white border-l flex flex-col">
        <div class="p-4 border-b font-bold text-lg bg-gray-50">Current Order</div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @if(empty($cart))
                <div class="text-center text-gray-400 mt-10">Cart is empty</div>
            @else
                @foreach($cart as $id => $item)
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-bold text-sm truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-gray-500">IDR {{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="p-1 bg-gray-200 rounded text-xs">-</button>
                            <span class="w-8 text-center text-sm font-bold">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="p-1 bg-gray-200 rounded text-xs">+</button>
                        </div>
                        <div class="w-20 text-right font-bold text-sm">
                            IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="p-4 bg-gray-50 border-t space-y-2">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>IDR {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-red-500">
                <span>Discount</span>
                <input type="number" wire:model.live="discount" class="w-24 text-right border rounded px-1">
            </div>
            <div class="flex justify-between font-bold text-xl border-t pt-2">
                <span>Total</span>
                <span>IDR {{ number_format($finalTotal, 0, ',', '.') }}</span>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-bold mb-1">Payment Method</label>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="$set('paymentMethod', 'cash')" class="py-2 border rounded-lg {{ $paymentMethod === 'cash' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700' }}">
                        Cash
                    </button>
                    <button wire:click="$set('paymentMethod', 'qris')" class="py-2 border rounded-lg {{ $paymentMethod === 'qris' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700' }}">
                        QRIS
                    </button>
                </div>
            </div>

            <button wire:click="checkout" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg mt-4 hover:bg-blue-700 transition">
                CHECKOUT
            </button>
        </div>
    </div>

    <!-- Notification -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail[0].message; type = $event.detail[0].type; setTimeout(() => show = false, 3000)"
         x-show="show"
         class="fixed bottom-4 right-4 p-4 rounded-lg shadow-lg text-white z-50"
         :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
         style="display: none;">
        <span x-text="message"></span>
    </div>
</div>
