<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function collection()
    {
        return Participant::where('group_id', $this->groupId)
            ->where('is_active', true)
            ->orderBy('lottery_number')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Undian',
            'Nama',
            'NIK',
            'Bag/Shift',
            'Shift',
            'Status Aktif',
            'Sudah Menang',
            'Tanggal Menang'
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->lottery_number,
            $participant->name,
            $participant->nik,
            $participant->department,
            $participant->shift,
            $participant->is_active ? 'Aktif' : 'Tidak Aktif',
            $participant->has_won ? 'Sudah' : 'Belum',
            $participant->won_at ? $participant->won_at->format('d/m/Y') : '-'
        ];
    }
}
