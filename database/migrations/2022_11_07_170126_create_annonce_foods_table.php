<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnonceFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annonce_foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('food_id')->unsigned();
            $table->integer('annonce_id')->unsigned();
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('annonce_id')->references('id')->on('annonces')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('annonce_foods');
    }
}
