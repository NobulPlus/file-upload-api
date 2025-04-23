<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($token, Request $request)
    {
        $session = UploadSession::where('token', $token)->firstOrFail();

        if ($session->isExpired()) {
            return response()->json(['message' => 'Link has expired'], 410);
        }

        if ($session->password && !password_verify($request->password, $session->password)) {
            return response()->json(['message' => 'Invalid password'], 403);
        }

        $session->increment('download_count');

        $file = $session->files->first(); // Stream first file for simplicity
        return Storage::disk('local')->download($file->file_path, $file->file_name);
    }
}