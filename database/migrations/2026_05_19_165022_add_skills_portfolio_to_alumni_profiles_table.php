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
    Schema::table('alumni_profiles', function (Blueprint $table) {
        $table->string('portfolio_url')->nullable()->after('linkedin_url');
        $table->text('skills')->nullable()->after('bio');
    });
}

public function down(): void
{
    Schema::table('alumni_profiles', function (Blueprint $table) {
        $table->dropColumn(['portfolio_url', 'skills']);
    });
}
};
