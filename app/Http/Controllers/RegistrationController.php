<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function index()
    {
        $activeGroups = Group::where('is_registration_active', true)
            ->withCount('participants')
            ->get();

        return view('registration.index', compact('activeGroups'));
    }

    public function form($groupId)
    {
        // Fetch group with participants for displaying the list
        $group = Group::where('is_registration_active', true)
            ->withCount('participants')
            ->with(['participants' => function($query) {
                $query->with('winner')
                     ->orderBy('registration_status', 'desc') // Pending/Rejected/Approved sorting
                     ->orderBy('lottery_number'); // Match admin view sorting
            }])
            ->findOrFail($groupId);

        // Check if full
        if ($group->participants_count >= $group->max_participants) {
            return redirect()->route('register.index')->with('error', 'Maaf, kuota untuk kelompok ini sudah penuh.');
        }

        return view('registration.form', compact('group'));
    }

    public function store(Request $request, $groupId)
    {
        $group = Group::where('is_registration_active', true)
            ->withCount('participants')
            ->findOrFail($groupId);

        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:50',
            'shift' => 'required|string|max:100',
            'account_count' => 'required|integer|min:1|max:10',
            'agreement' => 'accepted',
        ]);

        // Check if adding this count would exceed max participants
        if (($group->participants_count + $request->account_count) > $group->max_participants) {
            $remaining = $group->max_participants - $group->participants_count;
            return redirect()->back()->withInput()->with('error', "Maaf, kuota tersisa untuk kelompok ini adalah {$remaining} undian. Anda tidak bisa mendaftar {$request->account_count} undian.");
        }

        $participants = [];
        $suffixes = ['', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        for ($i = 0; $i < $request->account_count; $i++) {
            // Find existing accounts for this NIK in THIS group to determine suffix
            $existingCount = Participant::where('nik', $request->nik)
                ->where('group_id', $group->id)
                ->count();
            
            // Generate lottery number: NIK-GroupID + Suffix
            // We use existingCount + current iteration $i to find the next available suffix
            // But wait, existingCount already counts what's in DB.
            // Let's find suffixes already used.
            $baseLottery = $request->nik . '-' . $group->id;
            
            $nextSuffixIndex = $existingCount;
            $lotteryNumber = $baseLottery . ($nextSuffixIndex > 0 ? $suffixes[$nextSuffixIndex] : '');
            
            // Double check if generated lottery number exists (safety)
            while (Participant::where('lottery_number', $lotteryNumber)->exists()) {
                $nextSuffixIndex++;
                if ($nextSuffixIndex >= count($suffixes)) break; // Safety
                $lotteryNumber = $baseLottery . $suffixes[$nextSuffixIndex];
            }

            $participant = new Participant();
            $participant->group_id = $group->id;
            $participant->name = $request->name;
            $participant->nik = $request->nik;
            $participant->shift = $request->shift;
            $participant->lottery_number = $lotteryNumber;
            // Default password is the lottery number
            $participant->password = Hash::make($lotteryNumber);
            $participant->registration_status = 'pending';
            $participant->is_active = false;
            
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $i . '_registrant_' . $request->nik . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/participants', $filename, 'public');
                $participant->photo = 'uploads/participants/' . $filename;
            }

            $participant->save();
            $participants[] = $participant;
        }

        return view('registration.success', compact('group', 'participants'));
    }
}
