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
        Schema::create('pld', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->decimal('dia_num')->nullable();
            $table->decimal('hora')->nullable();
            $table->text('submercado')->nullable();
            $table->decimal('valor')->nullable();
            $table->string('mes_ref')->nullable();
            $table->decimal('dia_da_semana')->nullable();
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
        Schema::dropIfExists('pld');
    }
};
