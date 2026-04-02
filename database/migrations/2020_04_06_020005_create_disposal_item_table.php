<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposalItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('disposal_items', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->unsignedBigInteger('request_id');
//            $table->string('company_name')->nullable();
//            $table->string('name');
//            $table->string('asset_tye')->nullable();
//            $table->string('code')->nullable();
//            $table->string('model')->nullable();
//            $table->dateTime('purchase_date')->nullable();
//            $table->dateTime('broken_date')->nullable();
//            $table->integer('qty');
//            $table->string('desc')->nullable();
//            $table->string('attachment')->nullable();
//
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposal_items');
    }
}
