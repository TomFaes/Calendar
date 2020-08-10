<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->date('begin');
            $table->date('end');
            $table->integer('active')->nullable()->default(1);;
            $table->integer('group_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('admin_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasons');
    }
}
