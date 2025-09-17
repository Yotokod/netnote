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
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Interrogation, Devoir, Composition, etc.
            $table->foreignId('school_id')->constrained();
            $table->decimal('weight', 5, 2)->default(1.00); // Poids dans le calcul de moyenne
            $table->timestamps();
            
            $table->index(['school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_types');
    }
};
