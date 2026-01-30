@extends('livewire.reports.pdf.layout')

@section('title', 'Low Stock Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Low Stock Report</p>
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
                <th class="text-right">Current Stock</th>
                <th class="text-right">Min Stock</th>
                <th class="text-right">Below Min</th>
                <th>Status</th>
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
                    <td class="text-right">{{ $product->minimum_stock - $product->current_stock }}</td>
                    <td>
                        @php
                            $status = $product->stockStatus;
                            $badgeClass = $status['status'] === 'Out of Stock' ? 'badge-red' : 'badge-amber';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status['status'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Low Stock Products</div>
                <div class="value">{{ $products->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Out of Stock</div>
                <div class="value">{{ $products->filter(fn($p) => $p->current_stock === 0)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Critical (Below Min)</div>
                <div class="value">{{ $products->filter(fn($p) => $p->current_stock > 0 && $p->current_stock < $p->minimum_stock)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">At Minimum</div>
                <div class="value">{{ $products->filter(fn($p) => $p->current_stock === $p->minimum_stock)->count() }}</div>
            </div>
        </div>
    </div>
@endsection
