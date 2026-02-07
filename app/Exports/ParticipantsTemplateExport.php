<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantsTemplateExport implements WithHeadings, WithStyles, WithCustomStartCell, WithTitle
{
    public function headings(): array
    {
        // These headers must match what ParticipantsImport expects (case-insensitive usually, but strict on names if specified)
        // ParticipantsImport looks for 'nama'/'name', 'nik', 'bag_shift'/'bagshift'/'shift'/'bagian'
        return [
            'NAMA',
            'NIK',
            'BAGIAN', 
        ];
    }

    public function startCell(): string
    {
        // Headers will be written starting at A5
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        // Add instructions in A1 to A4
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT PESERTA ARISAN');
        $sheet->setCellValue('A2', 'Instruksi Pengisian:');
        $sheet->setCellValue('A3', '1. Jangan ubah header kolom di baris 5.');
        $sheet->setCellValue('A4', '2. Isi data mulai dari baris 6. NAMA dan NIK wajib diisi.');

        // Merge cells for title
        $sheet->mergeCells('A1:C1');

        // Style the instructions
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A4')->getFont()->setItalic(true);

        // Style the header row (Row 5)
        $sheet->getStyle('A5:C5')->getFont()->setBold(true);
        $sheet->getStyle('A5:C5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
        
        // Auto size columns
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Template Peserta';
    }
}
