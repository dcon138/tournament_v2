<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->unique();
            $table->integer('client_group_id')->unsigned();
            $table->foreign('client_group_id')->references('id')->on('client_groups');
            $table->string('name', 255)->unique();
            $table->string('short_name', 100)->nullable();
            $table->string('abn', 20)->unique()->nullable();
            $table->integer('primary_contact_id')->unsigned();
            $table->foreign('primary_contact_id')->references('id')->on('users');
            $table->string('address', 255)->nullable();
            $table->string('address2', 255)->nullable();
            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states');
            $table->string('suburb', 255)->nullable();
            $table->string('postcode', 16)->nullable();
            $table->string('bank_details', 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clients');
    }
}
