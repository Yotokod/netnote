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
        Schema::create('class_subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('academic_year_id')->constrained();
            $table->timestamps();
            
            $table->unique(['school_class_id', 'subject_id', 'teacher_id', 'academic_year_id'], 'class_subject_teacher_unique');
            $table->index(['teacher_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject_teacher');
    }
};
