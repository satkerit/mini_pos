<div class="flex justify-center py-1">
    @if($getRecord()->barcode || $getRecord()->sku)
        <img src="{{ route('barcode.generate', $getRecord()) }}"
             alt="{{ $getRecord()->barcode ?: $getRecord()->sku }}"
             style="height: 30px; max-width: 120px;"
             class="block">
    @else
        <span class="text-gray-400 text-xs">-</span>
    @endif
</div>
