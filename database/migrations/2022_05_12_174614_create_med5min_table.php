<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('med_5min', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('origem');
            $table->decimal('dia_num');
            $table->integer('minuto');
            $table->decimal('ativa_consumo');
            $table->decimal('ativa_geracao');
            $table->decimal('reativa_consumo');
            $table->decimal('reativa_geracao');
            $table->text('ponto');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('med_5min');
    }
};
