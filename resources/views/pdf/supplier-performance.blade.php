@extends('livewire.reports.pdf.layout')

@section('title', 'Supplier Performance Report')

@section('content')
    <div class="meta-info">
        <p><strong>Report Type:</strong> Supplier Performance Report</p>
        <p><strong>Period:</strong> {{ $startDate }} - {{ $endDate }}</p>
        <p><strong>Generated On:</strong> {{ $generatedAt }}</p>
        <p><strong>Total Suppliers:</strong> {{ $suppliers->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th class="text-right">Total Orders</th>
                <th class="text-right">Received</th>
                <th class="text-right">Total Spent (₦)</th>
                <th class="text-right">Avg Delivery (days)</th>
                <th class="text-right">On-Time Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $data)
                <tr>
                    <td>{{ $data['supplier']->company_name }}</td>
                    <td class="text-right">{{ $data['total_orders'] }}</td>
                    <td class="text-right">{{ $data['received_orders'] }}</td>
                    <td class="text-right">{{ number_format($data['total_spent'], 2) }}</td>
                    <td class="text-right">{{ $data['avg_delivery_time'] ?? 'N/A' }}</td>
                    <td class="text-right">
                        @if($data['on_time_delivery_rate'])
                            {{ number_format($data['on_time_delivery_rate'], 1) }}%
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Performance Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Suppliers</div>
                <div class="value">{{ $suppliers->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Orders</div>
                <div class="value">{{ $suppliers->sum('total_orders') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Spent</div>
                <div class="value">₦{{ number_format($totalSpent, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Average Orders per Supplier</div>
                <div class="value">{{ $suppliers->count() > 0 ? round($suppliers->sum('total_orders') / $suppliers->count(), 1) : 0 }}</div>
            </div>
        </div>
    </div>
@endsection
