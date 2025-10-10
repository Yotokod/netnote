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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('profession')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained();
            $table->foreignId('religion_id')->nullable()->constrained();
            $table->timestamps();
            
            $table->index(['first_name', 'last_name']);
            $table->index(['phone1']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
