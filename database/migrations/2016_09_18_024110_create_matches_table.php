<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('scoring_system_id')->unsigned();
            $table->foreign('scoring_system_id')->references('id')->on('scoring_systems');
            $table->integer('player_1_id')->unsigned();
            $table->foreign('player_1_id')->references('id')->on('players');
            $table->integer('player_2_id')->unsigned();
            $table->foreign('player_2_id')->references('id')->on('players');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('matches');
    }
}
