<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\Group;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParticipantsImport implements ToCollection, WithHeadingRow
{
    protected $groupId;
    protected $batchSize = 500;
    public $errors = [];

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function headingRow(): int
    {
        return 5;
    }

    public function collection(Collection $rows)
    {
        $participants = [];
        $nikCountsInFile = [];
        $nikCurrentOccurrence = [];
        
        // Step 1: Pre-calculate counts in the file
        foreach ($rows as $row) {
            $rowData = $row->toArray();
            $data = [];
            foreach ($rowData as $key => $val) {
                $data[strtolower(str_replace([' ', '/', '\\', '-'], '_', $key))] = $val;
            }
            
            $name = $data['nama'] ?? $data['name'] ?? null;
            $nikData = $data['nik'] ?? null;
            
            if (!empty($name) && !empty($nikData)) {
                $nik = trim((string)$nikData);
                $nikCountsInFile[$nik] = ($nikCountsInFile[$nik] ?? 0) + 1;
            }
        }

        // Step 2: Process rows
        foreach ($rows as $index => $row) {
            $rowData = $row->toArray();
            $data = [];
            foreach ($rowData as $key => $val) {
                $data[strtolower(str_replace([' ', '/', '\\', '-'], '_', $key))] = $val;
            }

            $name = $data['nama'] ?? $data['name'] ?? null;
            $nikData = $data['nik'] ?? null;
            $bagShift = $data['bag_shift'] ?? $data['bagshift'] ?? $data['shift'] ?? $data['bagian'] ?? null;

            if (empty($name) && empty($nikData)) {
                continue;
            }

            if (!$name || !$nikData) {
                $reason = "";
                if (!$name) $reason .= "NAMA kosong. ";
                if (!$nikData) $reason .= "NIK kosong. ";
                $this->errors[] = "Baris " . ($index + 6) . ": " . $reason; 
                continue;
            }

            $nik = trim((string)$nikData);

            // Count how many exist in THIS specific group (DB + File)
            $existingInThisGroup = Participant::where('group_id', $this->groupId)
                ->where('nik', $nik)
                ->count();
            $countInFile = $nikCountsInFile[$nik] ?? 0;
            $totalInThisGroup = $existingInThisGroup + $countInFile;
            
            $nikCurrentOccurrence[$nik] = ($nikCurrentOccurrence[$nik] ?? 0) + 1;

            // Generate lottery number based on count in THIS group
            if ($totalInThisGroup === 1) {
                // Only one account in this group
                $lotteryNumber = $nik . '-' . $this->groupId;
            } else {
                // Multiple accounts in this group
                // Format: [NIK]-[groupId][Letter]
                $suffixIndex = $existingInThisGroup + $nikCurrentOccurrence[$nik] - 1;
                $suffix = chr(65 + $suffixIndex); 
                $lotteryNumber = $nik . '-' . $this->groupId . $suffix;
            }

            $participants[] = [
                'group_id' => $this->groupId,
                'lottery_number' => $lotteryNumber,
                'name' => trim($name),
                'nik' => trim($nik),
                'department' => $bagShift ? trim((string)$bagShift) : '-',
                'shift' => $bagShift ? trim((string)$bagShift) : '-',
                'password' => Hash::make($lotteryNumber),
                'is_active' => true,
                'is_password_changed' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (empty($participants)) {
            $this->errors[] = "Tidak ditemukan data peserta yang valid untuk diimpor. Pastikan judul kolom dimulai pada baris ke-5 dengan nama NAMA dan NIK.";
            return;
        }

        // Process in batches to avoid memory issues
        $participantChunks = array_chunk($participants, $this->batchSize);
        
        DB::beginTransaction();
        try {
            foreach ($participantChunks as $chunk) {
                Participant::insert($chunk);
            }
            DB::commit();
            
            Log::info('Successfully imported ' . count($participants) . ' participants');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
