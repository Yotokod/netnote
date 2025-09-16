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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('school_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('school_class_id')->constrained();
            $table->string('matricule')->unique(); // Numéro matricule unique
            $table->enum('status', ['active', 'suspended', 'graduated', 'dropped'])->default('active');
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['matricule']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
