<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSTSKRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('stsk_request_items', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('request_id');
//
//            $table->timestamps();
//            $table->foreign('request_id')->references('id')->on('stsk_requests')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stsk_request_items');
    }
}
