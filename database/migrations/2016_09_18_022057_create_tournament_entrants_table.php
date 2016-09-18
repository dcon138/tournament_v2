<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentEntrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_entrants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('tournament_grade_id')->unsigned();
            $table->foreign('tournament_grade_id')->references('id')->on('tournament_grades');
            $table->integer('player_id')->unsigned();
            $table->foreign('player_id')->references('id')->on('players');
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
        Schema::drop('tournament_entrants');
    }
}
