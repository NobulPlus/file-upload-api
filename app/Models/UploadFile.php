<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    protected $fillable = ['upload_session_id', 'file_name', 'file_path', 'file_size', 'mime_type'];
}