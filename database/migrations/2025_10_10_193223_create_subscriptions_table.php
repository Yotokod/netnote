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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained();
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();
            
            $table->index(['school_id', 'status']);
            $table->index(['ends_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
