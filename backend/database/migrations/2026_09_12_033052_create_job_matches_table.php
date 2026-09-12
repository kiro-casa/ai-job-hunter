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
    Schema::create('job_matches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->foreignId('resume_id')->constrained()->onDelete('cascade');
        $table->decimal('overall_score', 5, 2);
        $table->decimal('skills_score', 5, 2);
        $table->decimal('experience_score', 5, 2);
        $table->decimal('responsibilities_score', 5, 2);
        $table->decimal('education_score', 5, 2);
        $table->decimal('keywords_score', 5, 2);
        $table->decimal('preferences_score', 5, 2);
        $table->text('explanation')->nullable();
        $table->enum('recommendation', [
            'high_priority', 'good_match', 'possible_match', 
            'weak_match', 'not_recommended'
        ]);
        $table->timestamps();

        $table->unique(['user_id', 'job_id', 'resume_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_matches');
    }
};
