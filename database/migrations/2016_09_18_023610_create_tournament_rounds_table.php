<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('tournament_draw_id')->unsigned();
            $table->foreign('tournament_draw_id')->references('id')->on('tournament_draws');
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
        Schema::drop('tournament_rounds');
    }
}
