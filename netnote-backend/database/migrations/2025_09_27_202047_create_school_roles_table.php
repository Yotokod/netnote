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
        Schema::create('school_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Liste des permissions
            $table->boolean('can_assign_roles')->default(false); // Peut assigner des rôles
            $table->json('assignable_roles')->nullable(); // Rôles qu'il peut assigner
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_system')->default(false); // Rôle système (créé par super admin)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['school_id', 'is_active']);
            $table->index(['is_system', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_roles');
    }
};