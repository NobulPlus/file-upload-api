<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max per file
            'expires_in' => 'required|integer|min:1|max:7', // Days
            'email_to_notify' => 'nullable|email',
            'password' => 'nullable|string|min:6',
        ]);

        $files = $request->file('files');
        $expiresIn = $request->input('expires_in');
        $emailToNotify = $request->input('email_to_notify');
        $password = $request->input('password');

        $uploadSession = $this->uploadService->handleUpload($files, $emailToNotify, $expiresIn, $password);

        return response()->json([
            'success' => true,
            'download_link' => route('api.download', ['token' => $uploadSession->token]),
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function stats($token)
    {
        $session = UploadSession::where('token', $token)->firstOrFail();

        return response()->json([
            'total_size' => $session->files->sum('file_size'),
            'download_count' => $session->download_count,
            'expires_at' => $session->expires_at,
            'file_count' => $session->files->count(),
        ]);
    }
}