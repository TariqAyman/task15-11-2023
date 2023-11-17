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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payer_id')->references('id')->on('users')->onDelete('no action');
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('no action');
            $table->decimal('amount');
            $table->date('due_on');
            $table->decimal('vat');
            $table->boolean('is_vat_inclusive');
            $table->enum('status', ['unpaid','paid', 'outstanding', 'overdue'])->default('outstanding');
            $table->decimal('total_paid_amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
