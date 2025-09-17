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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('serie_id')->nullable()->constrained();
            $table->foreignId('school_id')->constrained();
            $table->integer('capacity')->default(30);
            $table->string('level')->nullable(); // Niveau (6ème, 5ème, etc.)
            $table->timestamps();
            
            $table->index(['school_id']);
            $table->index(['serie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
