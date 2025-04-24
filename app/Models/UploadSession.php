<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    protected $fillable = ['token', 'user_id', 'email_to_notify', 'expires_at', 'password', 'download_count'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function files()
    {
        return $this->hasMany(UploadFile::class, 'upload_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}