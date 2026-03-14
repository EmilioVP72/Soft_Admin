<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('fk3_id_payment')->nullable();
            $table->foreign('fk3_id_payment')->references('id_payment')->on('payments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['fk3_id_payment']);
            $table->dropColumn('fk3_id_payment');
        });
    }
};
