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
    Schema::create('resume_skills', function (Blueprint $table) {
        $table->id();
        $table->foreignId('resume_id')->constrained()->onDelete('cascade');
        $table->foreignId('skill_id')->constrained()->onDelete('cascade');
        $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
        $table->timestamps();
        
        $table->unique(['resume_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_skills');
    }
};
