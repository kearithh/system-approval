<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
//            $table->unsignedBigInteger('position_id');
            $table->mediumText('purpose')->nullable();
            $table->mediumText('reason')->nullable();
            $table->mediumText('remark')->nullable();
            $table->float('status')
                ->default(0)
                ->comment('');
            $table->integer('approving')->comment('Total number of person who will react');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('draft')->default(1);
            $table->float('total_amount_khr')->nullable();
            $table->float('total_amount_usd')->nullable();
            $table->integer('company_id');
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
        Schema::dropIfExists('requests');
    }
}
