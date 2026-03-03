<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('garage_payout_invoices', function (Blueprint $table) {
            $table->id();
            
            // Direct foreign key to garage_payouts (simple, no polymorphism)
            $table->foreignId('garage_payout_id')
                  ->constrained('garage_payouts')
                  ->onDelete('cascade')
                  ->unique(); // One invoice per payout
            
            $table->string('invoice_number')->unique(); // e.g., GPI-2024-00123
            $table->string('revolut_transaction_id')->nullable();
            
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('GBP');
            
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->timestamp('sent_at')->nullable();
            
            $table->string('pdf_path')->nullable(); // storage path
            $table->string('status')->default('issued'); // issued, sent, void, cancelled
            
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // extra data like VAT breakdown
            
            $table->timestamps();
            
            // Indexes for quick lookups
            $table->index(['garage_payout_id', 'status']);
            $table->index('invoice_number');
            $table->index('issue_date');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garage_payout_invoices');
    }
};
