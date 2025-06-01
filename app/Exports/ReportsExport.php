<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Farmer Name',
            'Disease',
            'Assigned Technician',
            'Status',
            'Remarks',
            'Date Reported',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->farmer_name,
            optional($report->technicianReports)->diagnosis_disease ? optional($report->technicianReports)->diagnosis_disease : optional($report->farmerReports)->disease,
            optional($report->technicians)->full_name ?? 'N/A',
            $report->status,
            $report->remarks ?? 'N/A',
            $report->created_at ? $report->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
