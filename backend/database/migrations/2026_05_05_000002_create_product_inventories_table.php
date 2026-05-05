<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->id('id_product_inventory');
            $table->unsignedBigInteger('fk1_id_product');

            // Cantidad que dice el ticket del proveedor
            $table->decimal('ticket_quantity', 10, 2);

            // Cantidad que se verificó físicamente (null = aún no verificado)
            $table->decimal('physical_quantity', 10, 2)->nullable();

            // Diferencia calculada: physical_quantity - ticket_quantity
            // Positivo = proveedor dejó más, Negativo = proveedor dejó menos
            $table->decimal('difference', 10, 2)->nullable();

            // Estado del registro de inventario
            // 'pending'   = ticket ingresado, pendiente de verificación física
            // 'verified'  = ya se verificó físicamente, cantidades correctas
            // 'discrepancy' = hay diferencia entre ticket y físico
            $table->enum('status', ['pending', 'verified', 'discrepancy'])->default('pending');

            $table->text('notes')->nullable();
            $table->timestamp('ticket_date'); // Fecha en que llegó el proveedor / ticket
            $table->timestamp('verified_at')->nullable(); // Fecha de verificación física
            $table->timestamps();

            $table->foreign('fk1_id_product')
                  ->references('id_product')
                  ->on('products')
                  ->onDelete('cascade');

            $table->index('fk1_id_product');
            $table->index('status');
            $table->index('ticket_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_inventories');
    }
};
