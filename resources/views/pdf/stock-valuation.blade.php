@extends('livewire.reports.pdf.layout')

@section('title', 'Stock Valuation Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Stock Valuation Report</p>
        <p><strong>Generated On:</strong> {{ $generatedAt }}</p>
        <p><strong>Total Products:</strong> {{ $products->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Cost Price (₦)</th>
                <th class="text-right">Selling Price (₦)</th>
                <th class="text-right">Cost Value (₦)</th>
                <th class="text-right">Selling Value (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                @php
                    $sellingValue = $product->selling_price * $product->current_stock;
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ $product->current_stock }}</td>
                    <td class="text-right">{{ number_format($product->cost_price, 2) }}</td>
                    <td class="text-right">{{ number_format($product->selling_price, 2) }}</td>
                    <td class="text-right">{{ number_format($product->stock_value, 2) }}</td>
                    <td class="text-right">{{ number_format($sellingValue, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Financial Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Products</div>
                <div class="value">{{ $products->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Cost Value</div>
                <div class="value">₦{{ number_format($totalCostValue, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Selling Value</div>
                <div class="value">₦{{ number_format($totalSellingValue, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Potential Profit</div>
                <div class="value">₦{{ number_format($potentialProfit, 2) }}</div>
            </div>
        </div>
    </div>
@endsection
