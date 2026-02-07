<?php

namespace App\Http\Controllers;

use App\Models\Management;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
        ]);

        $position = Position::findOrFail($request->position_id);
        $data = $request->only(['nama_lengkap', 'position_id']);
        $data['jabatan'] = $position->name;

        if ($request->hasFile('foto_profil')) {
            $photo = $request->file('foto_profil');
            $photoName = 'management/photos/' . time() . '_' . uniqid() . '.webp';
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($photo->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            Storage::disk('public')->put($photoName, (string) $image);
            $data['foto_profil'] = $photoName;
        }

        if ($request->filled('ttd_drawing')) {
            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'management/signatures/' . uniqid() . '.webp';
            
            // Convert drawing to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read(base64_decode($imageData))->toWebp(80);
            Storage::disk('public')->put($fileName, (string) $image);
            $data['ttd'] = $fileName;
        } elseif ($request->hasFile('ttd')) {
            $ttd = $request->file('ttd');
            $ttdName = 'management/signatures/' . time() . '_' . uniqid() . '.webp';
            
            // Process image: convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($ttd->getPathname())->toWebp(80);
            Storage::disk('public')->put($ttdName, (string) $image);
            $data['ttd'] = $ttdName;
        }

        Management::create($data);

        return redirect()->back()->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $management = Management::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ttd_drawing' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
        ]);

        $position = Position::findOrFail($request->position_id);
        $data = $request->only(['nama_lengkap', 'position_id']);
        $data['jabatan'] = $position->name;

        if ($request->hasFile('foto_profil')) {
            if ($management->foto_profil) {
                Storage::disk('public')->delete($management->foto_profil);
            }
            
            $photo = $request->file('foto_profil');
            $photoName = 'management/photos/' . time() . '_' . uniqid() . '.webp';
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($photo->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            Storage::disk('public')->put($photoName, (string) $image);
            $data['foto_profil'] = $photoName;
        }

        if ($request->filled('ttd_drawing')) {
            if ($management->ttd) {
                Storage::disk('public')->delete($management->ttd);
            }
            $imageData = $request->input('ttd_drawing');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $fileName = 'management/signatures/' . uniqid() . '.webp';
            
            // Convert drawing to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read(base64_decode($imageData))->toWebp(80);
            Storage::disk('public')->put($fileName, (string) $image);
            $data['ttd'] = $fileName;
        } elseif ($request->hasFile('ttd')) {
            if ($management->ttd) {
                Storage::disk('public')->delete($management->ttd);
            }
            
            $ttd = $request->file('ttd');
            $ttdName = 'management/signatures/' . time() . '_' . uniqid() . '.webp';
            
            // Process image: convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($ttd->getPathname())->toWebp(80);
            Storage::disk('public')->put($ttdName, (string) $image);
            $data['ttd'] = $ttdName;
        }

        $management->update($data);

        return redirect()->back()->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $management = Management::findOrFail($id);

        if ($management->foto_profil) {
            Storage::disk('public')->delete($management->foto_profil);
        }
        if ($management->ttd) {
            Storage::disk('public')->delete($management->ttd);
        }

        $management->delete();

        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus.');
    }
}
