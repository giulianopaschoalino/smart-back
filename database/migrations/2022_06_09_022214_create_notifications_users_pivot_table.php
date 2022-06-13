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
    public function up()
    {
        Schema::create('notificacoes_users', function (Blueprint $table) {
//            $table->id();
//            $table->bigInteger('notification_id')->unsigned()->nullable();
//            $table->foreign('notification_id')->references('id')->on('notificacoes')->onDelete('cascade');
//            $table->bigInteger('user_id')->unsigned()->nullable();
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//
//

            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('notification_id')->references('id')->on('notificacoes')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificacoes_users');
    }
};
