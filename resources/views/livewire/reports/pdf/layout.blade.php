<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title') - StockFlow Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.6;
        }

        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 12pt;
            opacity: 0.9;
        }

        .meta-info {
            background-color: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #2563eb;
        }

        .meta-info p {
            margin: 5px 0;
            font-size: 9pt;
        }

        .meta-info strong {
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }

        table thead {
            background-color: #f9fafb;
        }

        table th {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 2px solid #2563eb;
            font-weight: 600;
            color: #1f2937;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-box h3 {
            color: #1e40af;
            margin-bottom: 10px;
            font-size: 12pt;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .summary-item {
            padding: 8px;
            background-color: white;
            border-radius: 4px;
        }

        .summary-item .label {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .summary-item .value {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }

        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-amber {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-gray {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            padding: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>StockFlow</h1>
        <div class="subtitle">@yield('title')</div>
    </div>

    @yield('content')

    <div class="footer">
        Generated on {{ date('F d, Y \a\t h:i A') }} | Page <span class="pagenum"></span>
    </div>
</body>
</html>
