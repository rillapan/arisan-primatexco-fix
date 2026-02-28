<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\Group;
use App\Models\MonthlyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    public function index($groupId)
    {
        $group = Group::with(['monthlyPeriods' => function ($query) {
            $query->orderBy('period_start', 'desc');
        }, 'monthlyPeriods.documentations'])->findOrFail($groupId);
        return view('admin.documentations.index', compact('group'));
    }

    public function create($groupId)
    {
        $group = Group::with('monthlyPeriods')->findOrFail($groupId);
        return view('admin.documentations.create', compact('group'));
    }

    public function store(Request $request, $groupId)
    {
        $request->validate([
            'monthly_period_id' => 'required|exists:monthly_periods,id',
            'type' => 'required|in:image,video,text',
            'caption' => 'nullable|string|max:255',
            'text_content' => 'required_if:type,text',
            'file' => 'required_if:type,image,video|file|max:51200', // max 50MB
        ]);

        $content = '';
        if ($request->type === 'text') {
            $content = $request->text_content;
        } else {
            $file = $request->file('file');
            
            if ($request->type === 'image') {
                // Use Intervention Image for WebP conversion
                $filename = Str::random(20) . '.webp';
                $path = 'documentations/images/' . $filename;
                
                // Process image with Intervention Image: convert to WebP
                $imageManager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $image = $imageManager->read($file->getPathname())
                    ->toWebp(80); // Convert to WebP with 80% quality
                
                // Store using Public disk to ensure correct permissions (644)
                Storage::disk('public')->put($path, (string) $image);
                
                $content = $path;
            } else {
                // For video, store on public disk
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(20) . '.' . $extension;
                $file->storeAs('documentations/videos', $filename, 'public');
                $content = 'documentations/videos/' . $filename;
            }
        }

        Documentation::create([
            'monthly_period_id' => $request->monthly_period_id,
            'type' => $request->type,
            'content' => $content,
            'caption' => $request->caption,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumentasi berhasil ditambahkan.',
                'redirect' => route('admin.groups.documentations.index', $groupId)
            ]);
        }

        return redirect()->route('admin.groups.documentations.index', $groupId)
            ->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $documentation = Documentation::findOrFail($id);
        $group = $documentation->monthlyPeriod->group;
        return view('admin.documentations.edit', compact('documentation', 'group'));
    }

    public function update(Request $request, $id)
    {
        $documentation = Documentation::findOrFail($id);
        $groupId = $documentation->monthlyPeriod->group_id;

        $request->validate([
            'caption' => 'nullable|string|max:255',
            'text_content' => 'sometimes|required_if:type,text',
        ]);

        if ($documentation->type === 'text' && $request->has('text_content')) {
            $documentation->content = $request->text_content;
        }

        $documentation->caption = $request->caption;
        $documentation->save();

        return redirect()->route('admin.groups.documentations.index', $groupId)
            ->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $documentation = Documentation::findOrFail($id);
        $groupId = $documentation->monthlyPeriod->group_id;

        if ($documentation->type !== 'text') {
            Storage::disk('public')->delete($documentation->content);
        }

        $documentation->delete();

        return redirect()->route('admin.groups.documentations.index', $groupId)
            ->with('success', 'Dokumentasi berhasil dihapus.');
    }

    public function download($id)
    {
        $documentation = Documentation::findOrFail($id);

        if ($documentation->type === 'text') {
            abort(400, 'Dokumentasi teks tidak dapat didownload.');
        }

        $path = storage_path('app/public/' . $documentation->content);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filename = $documentation->caption 
            ? Str::slug($documentation->caption) . '.' . pathinfo($path, PATHINFO_EXTENSION)
            : basename($path);

        return response()->download($path, $filename);
    }

    public function showForParticipant($periodId)
    {
        $period = MonthlyPeriod::with(['documentations', 'group'])->findOrFail($periodId);

        return view('participant.documentations.index', compact('period'));
    }

    private function compressImage($source, $destination)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, 70); // 70 quality
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
            imagegif($image, $destination);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $destination, 6); // 0-9 scale, 6 is decent compression
        }
        
        if (isset($image)) {
            imagedestroy($image);
        } else {
            // Fallback if not supported type
            copy($source, $destination);
        }
    }
}
