<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messengers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('driver');
            $table->string('channel_id')->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->boolean('wants_notifications')->default(false);
            $table->timestamps();
            $table->unique(['driver', 'channel_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messengers');
    }
}
