@extends('livewire.reports.pdf.layout')

@section('title', 'Current Stock Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Current Stock Report</p>
        <p><strong>Generated On:</strong> {{ $generatedAt }}</p>
        <p><strong>Total Products:</strong> {{ $products->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Supplier</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Min</th>
                <th>Status</th>
                <th class="text-right">Value (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->supplier->company_name ?? 'N/A' }}</td>
                    <td class="text-right">{{ $product->current_stock }} {{ $product->unit_of_measure }}</td>
                    <td class="text-right">{{ $product->minimum_stock }}</td>
                    <td>
                        @php
                            $status = $product->stockStatus;
                            $badgeClass = 'badge-gray';
                            if ($status['status'] === 'In Stock') $badgeClass = 'badge-green';
                            elseif ($status['status'] === 'Low Stock') $badgeClass = 'badge-amber';
                            elseif ($status['status'] === 'Out of Stock') $badgeClass = 'badge-red';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status['status'] }}</span>
                    </td>
                    <td class="text-right">{{ number_format($product->stock_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Products</div>
                <div class="value">{{ $products->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Stock Value</div>
                <div class="value">₦{{ number_format($totalStockValue, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">In Stock</div>
                <div class="value">{{ $products->filter(fn($p) => $p->current_stock > $p->minimum_stock)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Low/Out of Stock</div>
                <div class="value">{{ $products->filter(fn($p) => $p->current_stock <= $p->minimum_stock)->count() }}</div>
            </div>
        </div>
    </div>
@endsection
