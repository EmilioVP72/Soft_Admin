<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('id_employee');
            $table->string('full_name')->index();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('document_type', 20)->default('DNI'); // DNI, RUC, etc
            $table->string('document_number')->unique();
            $table->enum('position', ['Manager', 'Supervisor', 'Cashier', 'Stock', 'Sales', 'Other'])->default('Other');
            $table->decimal('salary', 10, 2)->default(0);
            $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Active')->index();
            $table->date('hire_date');
            $table->date('end_date')->nullable();
            
            // Relaciones
            $table->foreignId('fk_id_user')
                ->nullable()
                ->constrained('users', 'id_user')
                ->onDelete('set null')
                ->comment('Relación con usuario del sistema');
            
            $table->foreignId('fk_id_store')
                ->constrained('stores', 'id_store')
                ->onDelete('cascade')
                ->comment('Tienda a la que pertenece el empleado');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
