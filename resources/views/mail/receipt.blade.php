@php
    $resident = $invoice->resident;
    $house = $invoice->house;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt for {{ $invoice->reference }}</title>
</head>
<body>
    <h1>Payment receipt</h1>
    <p>Hello {{ $resident->full_name ?? 'Resident' }},</p>
    <p>We recorded a payment against your invoice {{ $invoice->reference }}.</p>
    <ul>
        <li>Invoice period: {{ $invoice->billing_period }}</li>
        <li>House: {{ $house->house_code ?? '-' }}</li>
        @if ($payment)
            <li>Payment date: {{ $payment->payment_date }}</li>
            <li>Amount: KES {{ number_format($payment->amount, 2) }}</li>
            <li>Method: {{ ucfirst($payment->method) }}</li>
            <li>Reference: {{ $payment->reference ?? 'N/A' }}</li>
        @endif
        <li>Balance remaining: KES {{ number_format($invoice->balance, 2) }}</li>
    </ul>
    <p>Thank you.</p>
</body>
</html>
