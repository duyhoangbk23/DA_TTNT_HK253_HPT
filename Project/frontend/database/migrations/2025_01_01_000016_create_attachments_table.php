<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 50);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('related_type', 100);
            $table->unsignedBigInteger('related_id');
            $table->timestamps();
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
