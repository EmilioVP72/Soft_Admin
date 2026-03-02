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
        Schema::create('department_suppliers', function (Blueprint $table) {
            $table->decimal('montos', 12, 2);
            $table->foreignId('fk1_id_supplier')->constrained('suppliers', 'id_supplier');
            $table->foreignId('fk2_id_department')->constrained('departments', 'id_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_suppliers');
    }
};
