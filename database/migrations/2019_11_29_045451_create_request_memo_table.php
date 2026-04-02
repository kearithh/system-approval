<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestMemoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_memo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('no')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('title_en')->nullable();
            $table->string('title_km');
            $table->longText('desc');
            $table->longText('point');
            $table->mediumText('remark')->nullable();
            $table->dateTime('start_date');
            $table->string('khmer_date')->nullable();
            $table->integer('status')->default(1)->comment('1 pending, 2 approved, 3 reject');
            $table->integer('company_id')->nullable();
            $table->integer('practise_point')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('request_memo');
    }
}
