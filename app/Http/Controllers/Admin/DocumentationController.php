<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentationController extends Controller
{
    /**
     * Show the drive link management page for admin
     */
    public function index()
    {
        $setting = DB::table('settings')->where('key', 'drive_link')->first();
        $driveLink = $setting ? $setting->value : null;

        $settingCaption = DB::table('settings')->where('key', 'drive_caption')->first();
        $driveCaption = $settingCaption ? $settingCaption->value : null;

        return view('admin.documentations.index', compact('driveLink', 'driveCaption'));
    }

    /**
     * Save or update the drive link
     */
    public function store(Request $request)
    {
        $request->validate([
            'drive_link' => 'required|url',
            'drive_caption' => 'nullable|string|max:255',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'drive_link'],
            ['value' => $request->drive_link]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'drive_caption'],
            ['value' => $request->drive_caption]
        );

        return redirect()->route('admin.drive-link.index')
            ->with('success', 'Link Google Drive berhasil disimpan.');
    }

    /**
     * Delete the drive link
     */
    public function destroy()
    {
        DB::table('settings')->where('key', 'drive_link')->delete();
        DB::table('settings')->where('key', 'drive_caption')->delete();

        return redirect()->route('admin.drive-link.index')
            ->with('success', 'Link Google Drive berhasil dihapus.');
    }

    /**
     * Show the drive link for participants
     */
    public function showForParticipant()
    {
        $setting = DB::table('settings')->where('key', 'drive_link')->first();
        $driveLink = $setting ? $setting->value : null;

        $settingCaption = DB::table('settings')->where('key', 'drive_caption')->first();
        $driveCaption = $settingCaption ? $settingCaption->value : null;

        return view('participant.documentations.index', compact('driveLink', 'driveCaption'));
    }
}
