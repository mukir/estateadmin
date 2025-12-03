@php
    $resident = $invoice->resident;
    $house = $invoice->house;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder for invoice {{ $invoice->reference }}</title>
</head>
<body>
    <h1>Invoice reminder</h1>
    <p>Hello {{ $resident->full_name ?? 'Resident' }},</p>
    <p>This is a reminder that your invoice is due/overdue.</p>
    <ul>
        <li>Invoice: {{ $invoice->reference }}</li>
        <li>Period: {{ $invoice->billing_period }}</li>
        <li>House: {{ $house->house_code ?? '-' }}</li>
        <li>Total: KES {{ number_format($invoice->total_amount, 2) }}</li>
        <li>Paid: KES {{ number_format($invoice->amount_paid, 2) }}</li>
        <li>Balance: KES {{ number_format($invoice->balance, 2) }}</li>
        <li>Due date: {{ $invoice->due_date }}</li>
    </ul>
    <p>If you have already paid, please disregard. Otherwise, kindly settle at your earliest convenience.</p>
    <p>Thank you.</p>
</body>
</html>
