<?php

namespace App\Services;

use App\Mail\UploadNotification;
use App\Models\UploadSession;
use App\Models\UploadFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    public function handleUpload(array $files, ?string $email, int $expiresIn, ?string $password = null): UploadSession
    {
        $token = Str::random(32);
        $expiresAt = now()->addDays($expiresIn ?: 1);

        $session = UploadSession::create([
            'token' => $token,
            'user_id' => auth()->id(), // Ensure user is authenticated
            'email_to_notify' => $email,
            'expires_at' => $expiresAt,
            'password' => $password ? bcrypt($password) : null,
        ]);

        foreach ($files as $file) {
            $this->storeFile($file, $session);
        }

        if ($email) {
            try {
                Mail::to($email)->queue(new UploadNotification($session));
            } catch (\Exception $e) {
                \Log::error('Failed to queue email: ' . $e->getMessage());
            }
        }

        return $session;
    }

    protected function storeFile(UploadedFile $file, UploadSession $session): void
    {
        $path = $file->store('uploads', 'local');

        UploadFile::create([
            'upload_session_id' => $session->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }
}