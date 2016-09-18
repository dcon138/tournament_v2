<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('tournament_id')->unsigned();
            $table->foreign('tournament_id')->references('id')->on('tournaments');
            $table->integer('scoring_system_id')->unsigned();
            $table->foreign('scoring_system_id')->references('id')->on('scoring_systems');
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
        Schema::drop('tournament_grades');
    }
}
