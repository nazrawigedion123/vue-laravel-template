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
    Schema::create('blog_section_translations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('blog_section_id')->constrained('blog_sections')->onDelete('cascade');
        $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
        $table->string('title', 200)->nullable();
        $table->text('content')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_section_translations');
    }
};
