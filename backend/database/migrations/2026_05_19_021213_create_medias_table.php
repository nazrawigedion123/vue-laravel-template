<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('url');       // e.g., 'uploads/2026/my-photo.jpg' or YouTube link
            $table->string('mime_type'); // e.g., 'image/jpeg', 'video/mp4', 'external/youtube'
            $table->unsignedBigInteger('file_size')->nullable(); // In bytes
            $table->foreignId('user_id')->constrained(); // Who uploaded it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
