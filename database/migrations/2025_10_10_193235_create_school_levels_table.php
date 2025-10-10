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
        Schema::create('school_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // CP, CE1, CE2, CM1, CM2, 6ème, 5ème, etc.
            $table->string('slug')->unique();
            $table->string('category'); // primaire, collège, lycée
            $table->integer('level_order'); // Ordre du niveau (1, 2, 3...)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category', 'level_order']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_levels');
    }
};
