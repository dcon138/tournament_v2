<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('client_groups', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('states', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users_clients', function (Blueprint $table) {
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('client_groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('states', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users_clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
