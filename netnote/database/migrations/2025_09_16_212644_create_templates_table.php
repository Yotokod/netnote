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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('kind', ['homepage', 'bulletin']); // Type de template
            $table->longText('html_content'); // Contenu HTML du template
            $table->json('shortcodes')->nullable(); // Liste des shortcodes disponibles
            $table->string('preview_path')->nullable(); // Image de prévisualisation
            $table->foreignId('author_id')->constrained('users');
            $table->string('version', 10)->default('1.0');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['kind']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
