@php
    $resident = $invoice->resident;
    $house = $invoice->house;
    $estate = $invoice->estate;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .box { border: 1px solid #e5e7eb; padding: 10px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .total { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>Invoice {{ $invoice->reference }}</h2>
            <p>Period: {{ $invoice->billing_period }}</p>
            <p>Date: {{ $invoice->invoice_date }}</p>
            <p>Due: {{ $invoice->due_date }}</p>
        </div>
        <div class="box">
            <p><strong>Resident</strong></p>
            <p>{{ $resident->full_name ?? 'Resident' }}</p>
            <p>{{ $resident->email }}</p>
            <p>{{ $resident->phone }}</p>
        </div>
    </div>

    <div class="box">
        <p><strong>Property</strong></p>
        <p>Estate: {{ $estate->name ?? '-' }}</p>
        <p>House: {{ $house->house_code ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align:right;">KES {{ number_format($item->amount * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total: KES {{ number_format($invoice->total_amount, 2) }}</p>
    <p>Paid: KES {{ number_format($invoice->amount_paid, 2) }}</p>
    <p>Balance: KES {{ number_format($invoice->balance, 2) }}</p>
</body>
</html>
