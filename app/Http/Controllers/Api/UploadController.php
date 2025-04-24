<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        Log::info('UploadController constructed');
    }

    public function upload(Request $request)
    {
        Log::info('UploadController::upload started', [
            'request_files' => $request->file('files'),
            'request_input' => $request->all(),
        ]);

        try {
            Log::info('Starting validation');
            $request->validate([
                'files' => 'required', // Accept single file or array
                'files.*' => 'file|max:10240', // Validate each file
                'expires_in' => 'required|integer|min:1|max:7',
                'email_to_notify' => 'nullable|email',
                'password' => 'nullable|string|min:6',
            ]);
            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        $files = $request->file('files');
        // Ensure $files is always an array
        $files = is_array($files) ? $files : [$files];
        Log::info('Files retrieved', ['file_count' => count($files)]);

        $expiresIn = $request->input('expires_in');
        $emailToNotify = $request->input('email_to_notify');
        $password = $request->input('password');

        Log::info('Calling UploadService::handleUpload', [
            'files_count' => count($files),
            'expires_in' => $expiresIn,
            'email' => $emailToNotify,
        ]);

        $uploadSession = $this->uploadService->handleUpload($files, $emailToNotify, $expiresIn, $password);

        Log::info('UploadSession created', ['token' => $uploadSession->token]);

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