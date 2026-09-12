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
    Schema::create('job_requirement_matches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('job_requirement_id')->constrained()->onDelete('cascade');
        $table->foreignId('resume_id')->constrained()->onDelete('cascade');
        $table->enum('match_type', [
            'exact', 'equivalent', 'related', 
            'partial', 'missing', 'uncertain'
        ]);
        $table->string('matched_value')->nullable();
        $table->text('match_details')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requirement_matches');
    }
};
