<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('tournament_round_id')->unsigned();
            $table->foreign('tournament_round_id')->references('id')->on('tournament_rounds');
            $table->integer('match_id')->unsigned();
            $table->foreign('match_id')->references('id')->on('matches');
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
        Schema::drop('tournament_matches');
    }
}
