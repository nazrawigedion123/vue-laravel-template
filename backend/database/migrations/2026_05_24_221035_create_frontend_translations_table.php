<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frontend_translations', function (Blueprint $table) {
            $table->id();
            // foreignId creates a BIGINT UNSIGNED column matching Laravel's default IDs
            // constrained() sets up the FK constraint; cascadeOnDelete matches on_delete=models.CASCADE
            $table->foreignId('language_id')
                  ->constrained()
                  ->cascadeOnDelete();
            
            $table->string('page', 100);
            $table->string('key', 255);
            $table->text('value');
            $table->timestamps(); // Optional: adds created_at and updated_at

            // Equivalent to unique_together = ("language", "page", "key")
            $table->unique(['language_id', 'page', 'key'], 'frontend_translations_unique_page_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frontend_translations');
    }
};