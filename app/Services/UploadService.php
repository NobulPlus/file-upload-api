<?php

namespace App\Services;

use App\Mail\UploadNotification;
use App\Models\UploadSession;
use App\Models\UploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadService
{
    public function handleUpload(array $files, ?string $email, int $expiresIn, ?string $password = null): UploadSession
    {
        Log::info('UploadService::handleUpload started', [
            'files_count' => count($files),
            'email' => $email,
            'expires_in' => $expiresIn,
        ]);

        $token = Str::random(32);
        $expiresAt = now()->addDays($expiresIn ?: 1);

        Log::info('Creating UploadSession');

        $session = UploadSession::create([
            'token' => $token,
            'user_id' => auth()->check() ? auth()->id() : null,
            'email_to_notify' => $email,
            'expires_at' => $expiresAt,
            'password' => $password ? bcrypt($password) : null,
        ]);

        Log::info('UploadSession created', ['session_id' => $session->id]);

        foreach ($files as $file) {
            $this->storeFile($file, $session);
        }

        if ($email) {
            try {
                Log::info('Queueing email notification', ['email' => $email]);
                Mail::to($email)->queue(new UploadNotification($session));
                Log::info('Email queued');
            } catch (\Exception $e) {
                Log::error('Failed to queue email: ' . $e->getMessage());
            }
        }

        return $session;
    }

    protected function storeFile(UploadedFile $file, UploadSession $session): void
    {
        Log::info('Storing file', ['filename' => $file->getClientOriginalName()]);

        $path = $file->store('uploads', 'local');

        Log::info('File stored', ['path' => $path]);

        UploadFile::create([
            'upload_session_id' => $session->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        Log::info('UploadFile record created');
    }
}