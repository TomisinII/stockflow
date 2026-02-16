<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Barcode Label - {{ $product->name }}</title>
    <style>
        @page {
            size: {{ $sizeConfig['width'] }} {{ $sizeConfig['height'] }};
            margin: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        .label {
            width: {{ $sizeConfig['width'] }};
            height: {{ $sizeConfig['height'] }};
            padding: 0.1in;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .product-name {
            font-size: {{ $sizeConfig['font-size'] }};
            font-weight: bold;
            margin-bottom: 0.05in;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        .barcode {
            margin: 0.05in 0;
        }
        
        .barcode svg {
            height: 0.6in;
        }
        
        .sku {
            font-size: calc({{ $sizeConfig['font-size'] }} - 1px);
            margin-top: 0.05in;
        }
        
        .price {
            font-size: {{ $sizeConfig['font-size'] }};
            font-weight: bold;
            margin-top: 0.05in;
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="product-name">{{ Str::limit($product->name, 30) }}</div>
        <div class="barcode">
            {!! $barcodeSVG !!}
        </div>
        <div class="sku">{{ $product->sku }}</div>
        <div class="price">₦{{ number_format($product->selling_price, 0) }}</div>
    </div>
</body>
</html>