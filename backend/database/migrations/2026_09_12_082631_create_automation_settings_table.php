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
    Schema::create('automation_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
        $table->boolean('auto_apply_enabled')->default(false);
        $table->boolean('require_confirmation')->default(true);
        $table->decimal('min_match_score', 5, 2)->default(80.00);
        $table->decimal('mandatory_threshold', 5, 2)->default(100.00);
        $table->decimal('min_confidence', 5, 4)->default(0.9500);
        $table->integer('max_applications_per_day')->default(10);
        $table->json('allowed_job_types')->nullable();
        $table->json('allowed_locations')->nullable();
        $table->json('work_arrangement_prefs')->nullable();
        $table->json('allowed_employment_types')->nullable();
        $table->boolean('auto_save_record')->default(true);
        $table->boolean('auto_generate_cover_letter')->default(false);
        $table->boolean('auto_attach_resume')->default(true);
        $table->boolean('auto_prepare_answers')->default(true);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_settings');
    }
};
