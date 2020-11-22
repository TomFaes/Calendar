<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayHourTypeToSeasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seasons', function(Blueprint $table) {
            $table->string('day')->nullable();
            $table->time('start_hour')->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seasons', function(Blueprint $table) {
            $table->string('day');
            $table->time('start_hour');
            $table->string('generating_type');
        });
    }
}
