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
        Schema::create('dados_te', function (Blueprint $table) {
            $table->bigInteger('cod_te');
            $table->bigInteger('cod_smart_unidade');
            $table->text('mes')->nullable();
            $table->text('operacao')->nullable();
            $table->text('tipo')->nullable();
            $table->decimal('montante_nf', 30, 10)->nullable();
            $table->decimal('preco_nf', 30, 10)->nullable();
            $table->decimal('nf_c_icms', 30, 10)->nullable();
            $table->text('perfil_contr')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('cod_smart_unidade', 'dados_te_cod_smart_unidade_fkey')
                ->references('cod_smart_unidade')
                ->on('dados_cadastrais')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->primary(['cod_te', 'cod_smart_unidade']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('dados_te');
    }
};
