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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('gender', ['M', 'F']);
            $table->string('blood_group')->nullable();
            $table->json('medical_info')->nullable(); // Maladies à déclarer, etc.
            $table->json('documents')->nullable(); // Paths vers documents
            $table->string('photo_path')->nullable();
            $table->json('parent_info')->nullable(); // Infos parents (nom, contact, etc.)
            $table->json('languages')->nullable(); // Langues parlées
            $table->timestamps();
            
            $table->index(['gender']);
            $table->index(['nationality']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
