<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryExport implements FromView, WithStyles
{
	use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['size' => 14], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
            2 => ['font' => ['size' => 11], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
            3 => ['font' => ['size' => 12], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
            5 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
            6 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
        ];
    }

    public function view(): View
    {
    	return view('admin/summary/salary/excel', [
    		'data' => $this->data
    	]);
    }
}