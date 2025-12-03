@php
    $resident = $invoice->resident;
    $house = $invoice->house;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->reference }}</title>
</head>
<body>
    <h1>Invoice {{ $invoice->reference }}</h1>
    <p>Hello {{ $resident->full_name ?? 'Resident' }},</p>
    <p>Please find your invoice attached.</p>
    <ul>
        <li>Period: {{ $invoice->billing_period }}</li>
        <li>House: {{ $house->house_code ?? '-' }}</li>
        <li>Total: KES {{ number_format($invoice->total_amount, 2) }}</li>
        <li>Due: {{ $invoice->due_date }}</li>
        <li>Status: {{ ucfirst($invoice->status) }}</li>
    </ul>
    <p>Thank you.</p>
</body>
</html>
