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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('published_by_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('comment_count')->default(0);
            $table->unsignedInteger('reaction_count')->default(0);
            $table->timestamps(); // Handles created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
