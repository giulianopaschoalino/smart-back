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
        Schema::create('economia', function (Blueprint $table) {
            $table->bigIncrements('cod_econ');
            $table->bigInteger('cod_smart_unidade');
            $table->text('mes text');
            $table->decimal('custo_cativo');
            $table->decimal('custo_livre');
            $table->decimal('economia_mensal');
            $table->decimal('economia_acumulada');
            $table->decimal('custo_unit');
            $table->boolean('dad_estimado');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cod_smart_unidade', 'economia_cod_smart_unidade_fkey')
                ->references('cod_smart_unidade')->on('dados_cadastrais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('economia');
    }
};
