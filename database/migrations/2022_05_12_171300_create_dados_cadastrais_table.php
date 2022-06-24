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
            $table->bigInteger('cod_smart_unidade', false)->primary()->default(null);
            $table->text('cliente')->nullable();
            $table->text('unidade')->nullable();
            $table->text('codigo_scde')->nullable();
            $table->decimal('demanda_p', 30, 10)->nullable();
            $table->decimal('demanda_fp', 30, 10)->nullable();
            $table->text('status_empresa')->nullable();
            $table->text('status_unidade')->nullable();
            $table->decimal('data_de_migracao', 30, 10)->nullable();
            $table->bigInteger('cod_smart_cliente')->nullable();
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
