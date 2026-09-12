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
    Schema::create('applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->foreignId('resume_id')->nullable()->constrained()->onDelete('set null');
        $table->enum('status', [
            'saved', 'qualified', 'ready_to_apply', 'applied', 
            'screening', 'interview', 'offer', 'accepted', 'rejected', 'withdrawn'
        ])->default('saved');
        $table->enum('application_method', ['manual', 'auto', 'api'])->default('manual');
        $table->enum('automation_status', [
            'not_applicable', 'pending', 'approved', 'blocked', 'submitted', 'failed'
        ])->default('not_applicable');
        $table->timestamp('applied_at')->nullable();
        $table->dateTime('interview_date')->nullable();
        $table->date('follow_up_date')->nullable();
        $table->json('contact_info')->nullable();
        $table->timestamps();

        $table->unique(['user_id', 'job_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
