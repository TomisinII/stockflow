@extends('livewire.reports.pdf.layout')

@section('title', 'Stock Movement Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Stock Movement Report</p>
        <p><strong>Period:</strong> {{ $startDate }} - {{ $endDate }}</p>
        <p><strong>Generated On:</strong> {{ $generatedAt }}</p>
        <p><strong>Total Adjustments:</strong> {{ $adjustments->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Type</th>
                <th class="text-right">Quantity</th>
                <th>Reason</th>
                <th>Adjusted By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adjustments as $adjustment)
                <tr>
                    <td>{{ $adjustment->adjustment_date->format('Y-m-d') }}</td>
                    <td>{{ $adjustment->product->name ?? 'N/A' }}</td>
                    <td>{{ $adjustment->product->sku ?? 'N/A' }}</td>
                    <td>{{ $adjustment->product->category->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $badgeClass = $adjustment->adjustment_type === 'in' ? 'badge-green' : 'badge-red';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($adjustment->adjustment_type) }}</span>
                    </td>
                    <td class="text-right">{{ abs($adjustment->quantity) }}</td>
                    <td>{{ $adjustment->reason }}</td>
                    <td>{{ $adjustment->adjustedBy->name ?? 'System' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Movement Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Adjustments</div>
                <div class="value">{{ $adjustments->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Stock In</div>
                <div class="value">{{ $stockIn }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Stock Out</div>
                <div class="value">{{ $stockOut }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Net Movement</div>
                <div class="value">{{ $stockIn - $stockOut }}</div>
            </div>
        </div>
    </div>
@endsection
