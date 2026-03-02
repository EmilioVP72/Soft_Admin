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
        Schema::create('stores', function (Blueprint $table) {
            $table->id('id_store');
            $table->string('store');
            $table->string('colony');
            $table->string('street');
            $table->integer('exterior_number');
            $table->integer('interior_number')->nullable();
            $table->text('reference')->nullable();
            $table->foreignId('fk1_id_locality')->constrained('localities', 'id_locality');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
