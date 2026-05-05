<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->string('product', 255);
            $table->string('barcode', 100)->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->string('unit', 50)->default('pieza'); // pieza, kg, litro, caja, etc.
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('fk1_id_supplier');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fk1_id_supplier')
                  ->references('id_supplier')
                  ->on('suppliers')
                  ->onDelete('cascade');

            $table->index('fk1_id_supplier');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
