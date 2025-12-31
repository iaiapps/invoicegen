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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict')
                ->comment('Customer ID - Should not be changed after invoice creation');
            $table->string('invoice_number', 50)->unique();
            $table->string('unique_id', 100)->unique();
            $table->date('invoice_date');
            $table->date('due_date');

            $table->bigInteger('subtotal')->default(0);
            // $table->decimal('tax_percentage', 5, 2)->default(0);  // Tetap decimal untuk %
            $table->bigInteger('tax_percentage')->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('total')->default(0);

            $table->text('notes')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('customer_id');
            $table->index('invoice_number');
            $table->index('unique_id');
            $table->index('status');
            $table->index('invoice_date');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
