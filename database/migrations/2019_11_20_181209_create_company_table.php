<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('companies', function (Blueprint $table) {
           $table->bigIncrements('id');
           $table->string('name');
           $table->string('long_name');
           $table->string('logo')->nullable();
           $table->string('footer')->nullable();
           $table->string('footer_landscape')->nullable();
           $table->integer('type');
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
        Schema::dropIfExists('companies');
    }
}
