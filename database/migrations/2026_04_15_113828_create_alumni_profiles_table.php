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
    Schema::create('alumni_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('student_id')->nullable();
        $table->string('course')->nullable();
        $table->integer('graduation_year')->nullable();
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->string('current_job')->nullable();
        $table->string('company')->nullable();
        $table->string('linkedin_url')->nullable();
        $table->string('profile_photo')->nullable();
        $table->text('bio')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
