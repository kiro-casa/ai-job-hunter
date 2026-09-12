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
    Schema::create('job_requirements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->text('requirement_text');
        $table->enum('requirement_type', ['skill', 'education', 'experience', 'certification', 'license', 'other']);
        $table->enum('classification', ['mandatory', 'preferred', 'nice_to_have', 'informational', 'unclear']);
        $table->string('normalized_value')->nullable(); // e.g., 'MySQL', 'Bachelor's in CS', '1 year'
        $table->string('min_value', 100)->nullable(); // For experience: '1 year'
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requirements');
    }
};
