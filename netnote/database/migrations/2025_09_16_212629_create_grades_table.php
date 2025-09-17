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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('assessment_id')->constrained();
            $table->decimal('mark', 8, 2);
            $table->decimal('out_of', 8, 2)->default(20.00);
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('school_id')->constrained();
            $table->timestamps();
            
            $table->unique(['student_id', 'assessment_id']);
            $table->index(['school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
