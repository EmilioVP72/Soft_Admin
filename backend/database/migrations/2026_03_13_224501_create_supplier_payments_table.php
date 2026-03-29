<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id('id_supplier_payment');
            $table->unsignedBigInteger('fk1_id_supplier');
            $table->unsignedBigInteger('fk2_id_department');
            $table->decimal('amount_paid', 12, 2);
            $table->timestamp('payment_date');
            $table->timestamps();

            $table->foreign('fk1_id_supplier')->references('id_supplier')->on('suppliers')->onDelete('cascade');
            $table->foreign('fk2_id_department')->references('id_department')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
