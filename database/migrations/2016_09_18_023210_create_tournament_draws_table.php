<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentDrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_draws', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('tournament_grade_id')->unsigned();
            $table->foreign('tournament_grade_id')->references('id')->on('tournament_grades');
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
        Schema::drop('tournament_draws');
    }
}
