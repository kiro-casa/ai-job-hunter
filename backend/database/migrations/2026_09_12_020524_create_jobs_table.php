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
    Schema::create('jobs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->string('company');
        $table->string('location')->nullable();
        $table->enum('work_arrangement', ['remote', 'hybrid', 'on_site', 'unspecified'])->default('unspecified');
        $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'unspecified'])->default('unspecified');
        $table->decimal('salary_min', 10, 2)->nullable();
        $table->decimal('salary_max', 10, 2)->nullable();
        $table->longText('description');
        $table->text('responsibilities')->nullable();
        $table->text('qualifications')->nullable();
        $table->string('source')->nullable(); // e.g., 'manual', 'adzuna', 'jsearch'
        $table->string('external_url', 1000)->nullable();
        $table->string('application_url', 1000)->nullable();
        $table->date('date_posted')->nullable();
        $table->timestamp('date_discovered')->useCurrent();
        $table->enum('status', ['new', 'saved', 'qualified', 'not_qualified', 'ready_to_apply', 'applied', 'screening', 'interview', 'offer', 'accepted', 'rejected', 'archived'])->default('new');
        $table->enum('application_method', ['manual', 'api', 'email', 'external_site', 'unknown'])->default('unknown');
        $table->boolean('automation_available')->default(false);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
