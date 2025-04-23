<?php

namespace App\Console\Commands;

use App\Models\UploadSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredUploads extends Command
{
    protected $signature = 'clean:expired-uploads';
    protected $description = 'Delete expired uploads and their files';

    public function handle(): void
    {
        $sessions = UploadSession::where('expires_at', '<', now())->get();

        foreach ($sessions as $session) {
            foreach ($session->files as $file) {
                Storage::disk('local')->delete($file->file_path);
                $file->delete();
            }
            $session->delete();
        }

        $this->info('Expired uploads cleaned successfully.');
    }
}