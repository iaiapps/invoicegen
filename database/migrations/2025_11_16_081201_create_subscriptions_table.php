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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Subscription Details
            $table->enum('plan', ['basic', 'pro']);
            $table->integer('invoice_limit');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->text('payment_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('payment_status');
            $table->index(['user_id', 'ends_at']);
            $table->index(['user_id', 'payment_status']);
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
