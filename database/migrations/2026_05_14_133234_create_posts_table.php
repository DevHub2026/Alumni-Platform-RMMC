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
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('category', [
            'career_update',
            'achievement',
            'opportunity',
            'reunion',
            'general'
        ])->default('general');
        $table->string('title');
        $table->text('body');
        $table->enum('status', ['visible', 'hidden', 'removed'])
              ->default('visible');
        $table->boolean('is_flagged')->default(false);
        $table->timestamps();

        // Indexes for performance
        $table->index('status');
        $table->index('category');
        $table->index('is_flagged');
    });
}

public function down(): void
{
    Schema::dropIfExists('posts');
}
};