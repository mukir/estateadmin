<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Resident;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function arrears()
    {
        $invoices = $this->reports->arrears();
        $byEstate = $invoices->groupBy('estate_id')->map(function ($rows) {
            return [
                'estate' => $rows->first()->estate,
                'total_balance' => $rows->sum('balance'),
                'invoices' => $rows->values(),
            ];
        })->values();

        return response()->json($byEstate);
    }

    public function collections(Request $request)
    {
        $summary = $this->reports->collectionsSummary($request->get('from'), $request->get('to'));

        return response()->json([
            'total' => $summary['total'],
            'by_method' => $summary['by_method'],
            'payments' => $summary['payments'],
        ]);
    }

    public function occupancy()
    {
        $estates = $this->reports->occupancy();

        $data = $estates->map(function (Estate $estate) {
            $planned = $estate->planned_units ?? $estate->planned_units_count ?? 0;
            $occupied = $estate->occupied_units ?? $estate->occupied_units_count ?? 0;
            $vacant = max($planned - $occupied, 0);
            $rate = $planned > 0 ? round(($occupied / $planned) * 100, 2) : 0;

            return [
                'estate' => $estate,
                'planned_units' => $planned,
                'occupied_units' => $occupied,
                'vacant_units' => $vacant,
                'occupancy_rate' => $rate,
            ];
        });

        return response()->json($data);
    }

    public function exportCsv(string $type)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$type}-report.csv\"",
        ];

        $rows = $this->getReportData($type);
        $out = fopen('php://temp', 'r+');

        if ($rows->isEmpty()) {
            fputcsv($out, ['message']);
            fputcsv($out, ['No data found']);
        } else {
            fputcsv($out, array_keys($rows->first()));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return Response::make($csv, 200, $headers);
    }

    protected function getReportData(string $type)
    {
        return match ($type) {
            'arrears' => $this->reports->arrears()->map(fn ($i) => [
                'billing_period' => $i->billing_period,
                'due_date' => $i->due_date,
                'estate' => $i->estate->name ?? '',
                'house' => $i->house->house_code ?? '',
                'resident' => $i->resident->full_name ?? '',
                'balance' => $i->balance,
                'status' => $i->status,
            ]),
            'payments' => $this->reports->payments()->map(fn ($p) => [
                'payment_date' => $p->payment_date,
                'amount' => $p->amount,
                'method' => $p->method,
                'invoice_period' => $p->invoice->billing_period ?? '',
                'resident' => $p->invoice->resident->full_name ?? '',
                'house' => $p->invoice->house->house_code ?? '',
            ]),
            'occupancy' => $this->reports->occupancy()->map(function ($e) {
                $planned = $e->planned_units ?? $e->planned_units_count ?? 0;
                $occupied = $e->occupied_units ?? $e->occupied_units_count ?? 0;
                $vacant = max($planned - $occupied, 0);
                $rate = $planned > 0 ? round(($occupied / $planned) * 100, 2) : 0;
                return [
                    'estate' => $e->name,
                    'planned_units' => $planned,
                    'occupied_units' => $occupied,
                    'vacant_units' => $vacant,
                    'occupancy_rate' => $rate,
                ];
            }),
            'invoice-status' => $this->reports->invoiceStatus()->map(fn ($i) => [
                'reference' => $i->reference,
                'billing_period' => $i->billing_period,
                'invoice_date' => $i->invoice_date,
                'due_date' => $i->due_date,
                'resident' => $i->resident->full_name ?? '',
                'house' => $i->house->house_code ?? '',
                'status' => $i->status,
                'balance' => $i->balance,
            ]),
            default => collect(),
        };
    }

    public function residentStatement(Resident $resident)
    {
        $invoices = $resident->invoices()->with(['items', 'payments', 'house', 'estate'])->orderBy('invoice_date')->get();
        $payments = $resident->payments()->orderBy('payment_date')->get();

        $runningBalance = 0;
        $statement = [];

        foreach ($invoices as $invoice) {
            $runningBalance += $invoice->total_amount;
            $statement[] = [
                'type' => 'invoice',
                'date' => $invoice->invoice_date,
                'reference' => $invoice->billing_period,
                'amount' => $invoice->total_amount,
                'balance' => $runningBalance,
            ];
        }

        foreach ($payments as $payment) {
            $runningBalance -= $payment->amount;
            $statement[] = [
                'type' => 'payment',
                'date' => $payment->payment_date,
                'reference' => $payment->reference,
                'amount' => $payment->amount * -1,
                'balance' => $runningBalance,
            ];
        }

        usort($statement, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return response()->json([
            'resident' => $resident,
            'statement' => $statement,
        ]);
    }
}
