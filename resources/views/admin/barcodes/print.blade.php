<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Barcodes</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            padding: 20px;
            background: #f8fafc;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            padding: 20px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 16px;
            color: white;
        }
        .header h1 { font-size: 20px; font-weight: 700; }
        .header p { font-size: 13px; opacity: 0.8; margin-top: 4px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .no-print button {
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s;
        }
        .no-print button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }
        .label {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            page-break-inside: avoid;
            transition: all 0.2s;
        }
        .label:hover {
            border-color: #a5b4fc;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }
        .label img { max-width: 150px; height: auto; display: block; margin: 0 auto 8px; }
        .label .name { font-size: 11px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
        .label .price { font-size: 10px; color: #64748b; }
        .label .sku { font-size: 9px; color: #94a3b8; margin-top: 4px; font-family: monospace; }
        @media print {
            .no-print { display: none; }
            .header { background: none; color: #1e293b; padding: 0 0 16px; }
            .header h1 { color: #1e293b; }
            .header p { color: #64748b; }
            body { background: white; padding: 10px; }
            .label { border: none; border-radius: 0; }
            .grid { gap: 8px; }
        }
    </style>
</head>
<body>
    <div class="header no-print">
        <h1>Barcode Labels</h1>
        <p>{{ count($products) }} {{ count($products) === 1 ? 'product' : 'products' }}</p>
    </div>

    <div class="no-print" style="margin-bottom:16px;">
        <button onclick="window.print()">
            <svg style="width:16px;height:16px;vertical-align:middle;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Barcodes
        </button>
    </div>

    <div class="grid">
        @foreach($products as $product)
        <div class="label">
            <img src="{{ route('barcode.generate', $product) }}" alt="{{ $product->barcode ?: $product->sku }}">
            <div class="name">{{ $product->name }}</div>
            <div class="price">IDR {{ number_format($product->price, 0, ',', '.') }}</div>
            @if($product->sku)<div class="sku">{{ $product->sku }}</div>@endif
        </div>
        @endforeach
    </div>
</body>
</html>
