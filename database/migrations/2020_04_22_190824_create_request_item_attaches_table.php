<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('request_item_attaches', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('stsk_request_item_id');
//            $table->string('name');
//            $table->string('original_name');
//            $table->string('src');
//
//            $table->timestamps();
//            $table->foreign('stsk_request_item_id')->references('id')->on('stsk_request_items')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_item_attaches');
    }
}
