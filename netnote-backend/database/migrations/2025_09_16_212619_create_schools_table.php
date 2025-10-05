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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('founder');
            $table->year('year_founded');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('quartier');
            $table->json('phones')->nullable(); // [phone1, phone2, phone3]
            $table->string('email_pro')->nullable();
            $table->text('about')->nullable();
            $table->text('bibliography')->nullable();
            $table->json('settings')->nullable(); // Template settings, 2FA, etc.
            $table->foreignId('template_id')->nullable()->constrained();
            $table->foreignId('bulletin_template_id')->nullable()->constrained('templates');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['subdomain']);
            $table->index(['custom_domain']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
