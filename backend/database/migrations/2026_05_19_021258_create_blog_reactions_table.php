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
    Schema::create('blog_reactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
        $table->string('reaction_type', 10)->default('like'); // like, love, haha, wow, sad, angry
        $table->timestamps();

        // Replaces Django's unique_together
        $table->unique(['user_id', 'blog_id']); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_reactions');
    }
};
