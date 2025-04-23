<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    protected $fillable = ['token', 'user_id', 'email_to_notify', 'expires_at', 'password', 'download_count'];

    public function files()
    {
        return $this->hasMany(UploadFile::class);
    }

    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }
}