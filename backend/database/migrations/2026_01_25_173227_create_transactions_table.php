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
            $table->id('id_transaction');
            $table->foreignId('fk1_id_store')->constrained('stores', 'id_store');
            $table->foreignId('fk2_id_user')->constrained('users', 'id_user');
            $table->decimal('total_amount', 12, 2);
            $table->string('transaction_type')->default('sale'); // sale, return, etc.
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
            
            $table->index('fk1_id_store');
            $table->index('fk2_id_user');
            $table->index('transaction_date');
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
