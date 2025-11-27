<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ActivityFileController extends Controller
{
    public function store(Request $request, Activity $activity)
    {
        // Hanya yang boleh update activity yang boleh upload lampiran
        Gate::authorize('update', $activity);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // max 10MB
        ]);

        $file = $data['file'];

        $path = $file->store("activities/{$activity->id}", 'public');

        $activity->files()->create([
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);

        return back()->with('success', 'File pendukung berhasil diunggah.');
    }

    public function download(ActivityFile $file)
    {
        $activity = $file->activity;

        // Siapa yang boleh lihat activity, boleh download file-nya
        Gate::authorize('view', $activity);

        $disk = Storage::disk('public');

        if (! $disk->exists($file->path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $absolutePath = $disk->path($file->path);

        return response()->download($absolutePath, $file->original_name);
    }

    public function destroy(ActivityFile $file)
    {
        $activity = $file->activity;

        Gate::authorize('update', $activity);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'File pendukung berhasil dihapus.');
    }
}
