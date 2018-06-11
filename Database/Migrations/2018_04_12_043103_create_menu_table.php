<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('menu_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned();
            $table->string('title');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('nickname')->unique();
            $table->string('before_html')->nullable();
            $table->string('after_html')->nullable();
            $table->string('target');
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('menu_item')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
        Schema::dropIfExists('menu_item');
    }
}
