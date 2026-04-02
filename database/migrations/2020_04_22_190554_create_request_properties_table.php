<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('request_properties', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('request_id');
//            $table->integer('name');
//            $table->integer('value');
//
//            $table->timestamps();
//            $table->foreign('request_id')->references('id')->on('stsk_requests')->onDelete('cascade');
//
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_properties');
    }
}
