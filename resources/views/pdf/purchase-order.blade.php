<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 10px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #6b7280;
            padding: 5px 10px 5px 0;
            width: 40%;
        }

        .info-value {
            display: table-cell;
            color: #1f2937;
            padding: 5px 0;
        }

        .supplier-box {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }

        .status-sent {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-received {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #f9fafb;
        }

        th {
            text-align: left;
            padding: 10px;
            font-size: 11px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }

        .totals-row.grand-total {
            border-top: 2px solid #2563eb;
            margin-top: 5px;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            clear: both;
        }

        .notes-box {
            background-color: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 12px;
            margin-top: 20px;
            font-size: 11px;
        }

        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">StockFlow</div>
            <div style="color: #6b7280; font-size: 11px;">Inventory Management System</div>
            <div class="document-title">PURCHASE ORDER</div>
        </div>

        <!-- PO Details -->
        <div class="section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">PO Number:</div>
                    <div class="info-value" style="font-weight: bold; color: #2563eb;">{{ $purchaseOrder->po_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Order Date:</div>
                    <div class="info-value">{{ $purchaseOrder->order_date->format('F d, Y') }}</div>
                </div>
                @if($purchaseOrder->expected_delivery_date)
                <div class="info-row">
                    <div class="info-label">Expected Delivery:</div>
                    <div class="info-value">{{ $purchaseOrder->expected_delivery_date->format('F d, Y') }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ $purchaseOrder->status }}">
                            {{ ucfirst($purchaseOrder->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created By:</div>
                    <div class="info-value">{{ $purchaseOrder->creator->name }}</div>
                </div>
                @if($purchaseOrder->received_by)
                <div class="info-row">
                    <div class="info-label">Received By:</div>
                    <div class="info-value">{{ $purchaseOrder->receiver->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Received At:</div>
                    <div class="info-value">{{ $purchaseOrder->received_at->format('F d, Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Supplier Information -->
        <div class="section">
            <div class="section-title">Supplier Information</div>
            <div class="supplier-box">
                <div style="font-weight: bold; font-size: 13px; color: #1f2937; margin-bottom: 8px;">
                    {{ $purchaseOrder->supplier->company_name }}
                </div>
                @if($purchaseOrder->supplier->contact_person)
                <div style="margin-bottom: 4px;">
                    <strong>Contact:</strong> {{ $purchaseOrder->supplier->contact_person }}
                </div>
                @endif
                @if($purchaseOrder->supplier->email)
                <div style="margin-bottom: 4px;">
                    <strong>Email:</strong> {{ $purchaseOrder->supplier->email }}
                </div>
                @endif
                @if($purchaseOrder->supplier->phone)
                <div style="margin-bottom: 4px;">
                    <strong>Phone:</strong> {{ $purchaseOrder->supplier->phone }}
                </div>
                @endif
                @if($purchaseOrder->supplier->address)
                <div style="margin-top: 8px;">
                    <strong>Address:</strong><br>
                    {{ $purchaseOrder->supplier->address }}
                    @if($purchaseOrder->supplier->city || $purchaseOrder->supplier->state)
                    <br>{{ $purchaseOrder->supplier->city }}@if($purchaseOrder->supplier->city && $purchaseOrder->supplier->state), @endif{{ $purchaseOrder->supplier->state }}
                    @endif
                    @if($purchaseOrder->supplier->zip_code)
                    {{ $purchaseOrder->supplier->zip_code }}
                    @endif
                </div>
                @endif
                @if($purchaseOrder->supplier->payment_terms)
                <div style="margin-top: 8px;">
                    <strong>Payment Terms:</strong> {{ $purchaseOrder->supplier->payment_terms }}
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="section">
            <div class="section-title">Order Items</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Product</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 15%; text-align: center;">Qty Ordered</th>
                        @if($purchaseOrder->status === 'received')
                        <th style="width: 15%; text-align: center;">Qty Received</th>
                        @endif
                        <th style="width: 15%; text-align: right;">Unit Cost</th>
                        <th style="width: 15%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->sku }}</td>
                        <td class="text-center">{{ number_format($item->quantity_ordered) }}</td>
                        @if($purchaseOrder->status === 'received')
                        <td class="text-center">
                            {{ number_format($item->quantity_received) }}
                            @if($item->quantity_received < $item->quantity_ordered)
                            <span style="color: #dc2626; font-size: 9px;">(Partial)</span>
                            @endif
                        </td>
                        @endif
                        <td class="text-right">#{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="text-right">#{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <span>#{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Amount:</span>
                    <span>#{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($purchaseOrder->notes)
        <div style="clear: both; padding-top: 20px;">
            <div class="notes-box">
                <div class="notes-title">Notes:</div>
                <div>{{ $purchaseOrder->notes }}</div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div style="margin-bottom: 5px;">
                <strong>StockFlow Inventory Management System</strong>
            </div>
            <div>
                Generated on {{ now()->format('F d, Y \a\t H:i') }}
            </div>
            <div style="margin-top: 10px; font-size: 9px; color: #9ca3af;">
                This is a computer-generated document. No signature is required.
            </div>
        </div>
    </div>
</body>
</html>
