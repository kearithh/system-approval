<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestHrItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_hr_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->string('name')->nullable();
            $table->string('desc')->nullable();
            $table->string('purpose')->nullable();
            $table->integer('qty');
            $table->string('unit');
            $table->float('unit_price');
            $table->string('account_no')->nullable();
            $table->float('balance')->nullable();
            $table->string('currency')->nullable();
            $table->string('remark')->nullable();
            $table->string('import')->nullable();
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
        Schema::dropIfExists('request_hr_items');
    }
}
