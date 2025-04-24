<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadSession;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download(Request $request, $token)
    {
        $session = UploadSession::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if ($session->password && !$request->has('password')) {
            return response()->json(['message' => 'Password required'], 403);
        }

        if ($session->password && !bcrypt($request->input('password'), $session->password)) {
            return response()->json(['message' => 'Invalid password'], 403);
        }

        $session->increment('download_count');

        $files = UploadFile::where('upload_session_id', $session->id)->get();

        if ($files->count() === 1) {
            $file = $files->first();
            return Storage::disk('local')->download($file->file_path, $file->file_name);
        }

        // For multiple files, create a ZIP
        $zip = new \ZipArchive();
        $zipFileName = 'uploads_' . $token . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile(storage_path('app/' . $file->file_path), $file->file_name);
            }
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}