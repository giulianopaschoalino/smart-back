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
        Schema::create('dados_cadastrais', function (Blueprint $table) {
            $table->bigIncrements('cod_smart_unidade');
            $table->text('cliente');
            $table->text('unidade');
            $table->text('codigo_scde');
            $table->decimal('demanda_p');
            $table->decimal('demanda_fp');
            $table->text('status_empresa');
            $table->text('status_unidade');
            $table->decimal('data_de_migracao');
            $table->bigInteger('cod_smart_cliente');
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
        Schema::dropIfExists('dados_cadastrais');
    }
};
