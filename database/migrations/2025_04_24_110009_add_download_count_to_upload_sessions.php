<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDownloadCountToUploadSessions extends Migration
{
    public function up()
    {
        Schema::table('upload_sessions', function (Blueprint $table) {
            $table->unsignedInteger('download_count')->default(0)->after('password');
        });
    }

    public function down()
    {
        Schema::table('upload_sessions', function (Blueprint $table) {
            $table->dropColumn('download_count');
        });
    }
}