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
        Schema::create('general_deps', function (Blueprint $table) {
            $table->id('id_general_dep');
            $table->string('g_departament');
            $table->text('g_descripcion');
            $table->foreignId('fkl_id_tienda')->constrained('stores', 'id_store');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_deps');
    }
};
