@extends('livewire.reports.pdf.layout')

@section('title', 'Purchase Orders Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Purchase Orders Report</p>
        <p><strong>Period:</strong> {{ $startDate }} - {{ $endDate }}</p>
        <p><strong>Generated On:</strong> {{ $generatedAt }}</p>
        <p><strong>Total Orders:</strong> {{ $orders->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Status</th>
                <th class="text-right">Items</th>
                <th class="text-right">Total (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->po_number }}</td>
                    <td>{{ $order->supplier->company_name ?? 'N/A' }}</td>
                    <td>{{ $order->order_date->format('Y-m-d') }}</td>
                    <td>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        @php
                            $badgeClass = 'badge-gray';
                            if ($order->status === 'sent') $badgeClass = 'badge-amber';
                            elseif ($order->status === 'received') $badgeClass = 'badge-green';
                            elseif ($order->status === 'cancelled') $badgeClass = 'badge-red';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="text-right">{{ $order->items->count() }}</td>
                    <td class="text-right">{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Order Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Orders</div>
                <div class="value">{{ $orders->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Amount</div>
                <div class="value">₦{{ number_format($totalAmount, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Draft Orders</div>
                <div class="value">{{ $orders->where('status', 'draft')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Sent Orders</div>
                <div class="value">{{ $orders->where('status', 'sent')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Received Orders</div>
                <div class="value">{{ $orders->where('status', 'received')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Cancelled Orders</div>
                <div class="value">{{ $orders->where('status', 'cancelled')->count() }}</div>
            </div>
        </div>
    </div>
@endsection
