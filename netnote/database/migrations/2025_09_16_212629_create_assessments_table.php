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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('assessment_type_id')->constrained();
            $table->foreignId('school_class_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('school_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->date('date');
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->decimal('total_mark', 8, 2)->default(20.00);
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id']);
            $table->index(['school_class_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
