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
    Schema::create('application_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('application_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('resume_id')->nullable()->constrained()->onDelete('set null');
        $table->decimal('match_score', 5, 2)->nullable();
        $table->string('eligibility_decision', 50)->nullable();
        $table->integer('required_count')->default(0);
        $table->integer('required_met')->default(0);
        $table->json('missing_required')->nullable();
        $table->integer('preferred_count')->default(0);
        $table->integer('preferred_met')->default(0);
        $table->json('missing_preferred')->nullable();
        $table->decimal('confidence', 5, 4)->nullable();
        $table->string('automation_provider', 255)->nullable();
        $table->timestamp('attempt_time')->useCurrent();
        $table->string('result', 50);
        $table->text('failure_reason')->nullable();
        $table->boolean('safety_gate_passed')->default(false);
        $table->json('safety_gate_details')->nullable();
        $table->json('generated_materials')->nullable();
        $table->string('status', 50);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_logs');
    }
};
