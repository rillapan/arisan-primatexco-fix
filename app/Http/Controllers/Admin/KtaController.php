<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KtaSetting;
use App\Models\Participant;
use Illuminate\Support\Facades\File;

class KtaController extends Controller
{
    public function settings()
    {
        $setting = KtaSetting::first() ?? new KtaSetting();
        return view('admin.kta.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'header_title' => 'required|string|max:255',
            'moto' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature_name' => 'required|string|max:255',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'vision' => 'required|string',
            'mission' => 'required|string',
        ]);

        $setting = KtaSetting::first() ?? new KtaSetting();
        
        $data = $request->only(['header_title', 'moto', 'signature_name', 'vision', 'mission']);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                $oldPath = public_path('uploads/kta/' . $setting->logo);
                if (File::exists($oldPath)) File::delete($oldPath);
            }
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.webp';
            
            if (!file_exists(public_path('uploads/kta'))) {
                mkdir(public_path('uploads/kta'), 0777, true);
            }
            
            // Process image: convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($logo->getPathname())->toWebp(80);
            file_put_contents(public_path('uploads/kta/' . $logoName), (string) $image);
            $data['logo'] = $logoName;
        }

        if ($request->hasFile('signature_image')) {
            if ($setting->signature_image) {
                $oldPath = public_path('uploads/kta/' . $setting->signature_image);
                if (File::exists($oldPath)) File::delete($oldPath);
            }
            $signature = $request->file('signature_image');
            $signatureName = 'signature_' . time() . '.webp';
            
            if (!file_exists(public_path('uploads/kta'))) {
                mkdir(public_path('uploads/kta'), 0777, true);
            }
            
            // Process image: convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($signature->getPathname())->toWebp(80);
            file_put_contents(public_path('uploads/kta/' . $signatureName), (string) $image);
            $data['signature_image'] = $signatureName;
        }

        if ($setting->exists) {
            $setting->update($data);
        } else {
            KtaSetting::create($data);
        }

        return redirect()->back()->with('success', 'Pengaturan KTA berhasil diperbarui.');
    }

    public function scanner()
    {
        return view('admin.kta.scanner');
    }

    public function search(Request $request)
    {
        $lotteryNumber = $request->input('lottery_number');
        $participant = Participant::with('group')
            ->where('lottery_number', $lotteryNumber)
            ->first();

        if (!$participant) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan.']);
        }

        return response()->json([
            'success' => true,
            'participant' => [
                'name' => $participant->name,
                'nik' => $participant->nik,
                'lottery_number' => $participant->lottery_number,
                'group_name' => $participant->group->name,
                'has_won' => $participant->has_won ? 'Sudah Menang' : 'Belum Menang',
                'is_active' => $participant->is_active ? 'Aktif' : 'Tidak Aktif',
            ]
        ]);
    }
}
