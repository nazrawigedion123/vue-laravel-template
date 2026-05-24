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
    Schema::create('user_roles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->boolean('can_create_blog')->default(false);
        $table->boolean('can_edit_blog')->default(false);
        $table->boolean('can_delete_blog')->default(false);
        $table->boolean('can_publish_blog')->default(false);
        $table->boolean('can_manage_users')->default(false);
        $table->boolean('can_create_media')->default(false);
        $table->boolean('can_edit_media')->default(false);
        $table->boolean('can_delete_media')->default(false);
        $table->boolean('can_manage_subscribers')->default(false);
        $table->boolean('can_manage_contacts')->default(false);
        $table->boolean('can_manage_settings')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
