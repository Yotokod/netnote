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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('coefficient', 5, 2)->default(1.00);
            $table->foreignId('school_id')->constrained();
            $table->json('grading_formula')->nullable(); // Formule de calcul des moyennes
            $table->timestamps();
            
            $table->index(['school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
