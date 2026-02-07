<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $guard = Auth::getDefaultDriver();
        
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->to('/');
    }

    public function showParticipantLoginForm()
    {
        return view('auth.participant-login');
    }

    public function participantLogin(Request $request)
    {
        $credentials = $request->validate([
            'lottery_number' => 'required|string',
            'password' => 'required|string'
        ]);

        $input = trim($credentials['lottery_number']);
        $passwordInput = trim($credentials['password']);

        // Helper to normalize input (handling dots, dashes, and spaces)
        $normalize = function($str) {
            $str = str_replace(['–', '—'], '-', $str);
            $str = str_replace(' ', '', $str);
            return $str;
        };

        $normalizedInput = $normalize($input);
        $pureNik = str_replace('.', '', $normalizedInput);

        // Find all possible candidates by Lottery Number or NIK
        $candidates = Participant::whereRaw('LOWER(lottery_number) = ?', [strtolower($normalizedInput)])
            ->orWhere('nik', $normalizedInput)
            ->orWhere('nik', $pureNik)
            ->orWhereRaw("REPLACE(nik, '.', '') = ?", [$pureNik])
            ->get();
        
        if ($candidates->isEmpty()) {
            return back()->withErrors([
                'lottery_number' => 'No. Undian atau NIK tidak ditemukan.',
            ])->onlyInput('lottery_number');
        }

        // Iterate through candidates and find the one that matches the password
        $matchedParticipant = null;
        foreach ($candidates as $participant) {
            // 1. Precise hash check
            if (\Hash::check($passwordInput, $participant->password)) {
                $matchedParticipant = $participant;
                break;
            } 
            // 2. Default password check (Case-Insensitive)
            elseif (!$participant->is_password_changed) {
                if (strtolower($passwordInput) === strtolower($participant->lottery_number) || 
                    strtolower($passwordInput) === strtolower($participant->nik)) {
                    $matchedParticipant = $participant;
                    break;
                }
            }
        }

        if (!$matchedParticipant) {
            return back()->withErrors([
                'lottery_number' => 'Kredensial tidak cocok. Pastikan Nomor Undian/NIK dan Password benar.',
            ])->onlyInput('lottery_number');
        }

        // Check if participant is active
        if (!$matchedParticipant->is_active) {
            return back()->withErrors([
                'lottery_number' => 'Akun Anda tidak aktif.',
            ])->onlyInput('lottery_number');
        }

        // Login the participant
        Auth::guard('participant')->login($matchedParticipant);
        $request->session()->regenerate();
        
        return redirect()->intended(route('participant.dashboard'));
    }
}
