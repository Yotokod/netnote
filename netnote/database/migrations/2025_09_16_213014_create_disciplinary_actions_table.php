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
        Schema::create('disciplinary_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('school_id')->constrained();
            $table->string('type'); // 'warning', 'detention', 'suspension', etc.
            $table->text('description');
            $table->date('date');
            $table->integer('penalty_points')->default(0);
            $table->timestamps();
            
            $table->index(['school_id', 'student_id']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_actions');
    }
};
