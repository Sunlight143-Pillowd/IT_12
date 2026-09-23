<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $pcBuild->build_number }} - PC Build</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: 700; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(180px, 1fr)); gap: 12px; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        th { background: #f3f4f6; }
        .total { margin-top: 16px; font-weight: 700; text-align: right; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Davao Boss Computer</div>
        <div style="margin-top: 6px; font-size: 12px; color: #4b5563;">PC Build Sheet</div>
    </div>

    <div class="meta">
        <div><strong>Build Number:</strong> {{ $pcBuild->build_number }}</div>
        <div><strong>Date:</strong> {{ $pcBuild->created_at->format('F d, Y') }}</div>
        <div><strong>Customer:</strong> {{ $pcBuild->customer_name ?: 'Walk-in Customer' }}</div>
        <div><strong>Status:</strong> {{ $pcBuild->status }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Component</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pcBuild->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                    <td>₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Total: ₱{{ number_format($pcBuild->total_cost, 2) }}</div>

    @if ($pcBuild->notes)
        <div style="margin-top: 20px; border-top: 1px solid #d1d5db; padding-top: 12px;">
            <strong>Notes:</strong><br />
            {{ $pcBuild->notes }}
        </div>
    @endif
</body>
</html>
