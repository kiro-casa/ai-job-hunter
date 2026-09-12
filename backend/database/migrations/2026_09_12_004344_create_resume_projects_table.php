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
    Schema::create('resume_projects', function (Blueprint $table) {
        $table->id();
        $table->foreignId('resume_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->text('description');
        $table->string('technologies')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_projects');
    }
};
