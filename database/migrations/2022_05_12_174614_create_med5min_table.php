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
            $table->text('origem')->nullable();
            $table->decimal('dia_num')->nullable();
            $table->integer('minuto')->nullable();
            $table->numeric('ativa_consumo')->nullable();
            $table->numeric('ativa_geracao')->nullable();
            $table->numeric('reativa_consumo')->nullable();
            $table->numeric('reativa_geracao')->nullable();
            $table->text('ponto')->nullable();
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
