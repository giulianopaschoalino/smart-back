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

            $table->bigInteger('cod_te')->unsigned();
            $table->bigInteger('cod_smart_unidade')->unsigned();
            $table->text('mes')->nullable()->default(null);
            $table->numeric('custo_cativo')->nullable();
            $table->numeric('custo_livre')->nullable();
            $table->numeric('economia_mensal')->nullable();
            $table->numeric('economia_acumulada')->nullable();
            $table->numeric('custo_unit')->nullable();
            $table->boolean('dad_estimado')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cod_smart_unidade', 'economia_cod_smart_unidade_fkey')
                ->references('cod_smart_unidade')->on('dados_cadastrais')->onDelete('no action')->onUpdate('no action')->notValid();

            $table->primary(['cod_te','cod_smart_unidade']);
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
