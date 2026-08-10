<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Application::with(['service', 'agent'])
            ->get()
            ->map(function ($a) {

                return [

                    'ID' => $a->id,
                    'Agent' => $a->agent->name,
                    'Service' => $a->service->name,
                    'Status' => $a->status->value,
                    'Payment' => $a->payment_status->value,
                    'Amount' => $a->amount,
                    'Created' => $a->created_at,

                ];

            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Agent',
            'Service',
            'Status',
            'Payment',
            'Amount',
            'Created',
        ];
    }
}
