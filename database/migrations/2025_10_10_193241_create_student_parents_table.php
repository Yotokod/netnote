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
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained()->onDelete('cascade');
            $table->enum('relationship_type', ['father', 'mother', 'guardian', 'tutor', 'other']);
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_pick_up')->default(true);
            $table->boolean('emergency_contact')->default(false);
            $table->timestamps();
            
            $table->unique(['student_id', 'guardian_id']);
            $table->index(['student_id', 'is_primary_contact']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
