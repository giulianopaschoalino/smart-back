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

            $table->bigInteger('cod_econ')->unsigned();
            $table->bigInteger('cod_smart_unidade')->unsigned();
            $table->text('mes')->nullable()->default(null);
            $table->decimal('custo_cativo', 30, 10)->nullable();
            $table->decimal('custo_livre', 30, 10)->nullable();
            $table->decimal('economia_mensal', 30, 10)->nullable();
            $table->decimal('economia_acumulada', 30, 10)->nullable();
            $table->decimal('custo_unit', 30, 10)->nullable();
            $table->boolean('dad_estimado')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cod_smart_unidade', 'economia_cod_smart_unidade_fkey')
                ->references('cod_smart_unidade')->on('dados_cadastrais')->onDelete('no action')->onUpdate('no action');

            $table->primary(['cod_econ', 'cod_smart_unidade']);
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
