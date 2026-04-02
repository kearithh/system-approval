<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSTSKRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('stsk_requests', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->integer('status')->comment('1 pending, 2 approved, 3 rejected, 4 re-pending');
//            $table->string('type')->comment('memo, se, ge, disposal, etc...');
//            $table->unsignedBigInteger('requester_id')->comment('user who create the request');
//            $table->unsignedBigInteger('company_id')->nullable()->comment('');
//            $table->string('branch_code', '191')->nullable()->comment('');
//            $table->bigInteger('department_id')->nullable()->comment('');
//
//            $table->foreign('requester_id')->references('id')->on('users');
//            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
////            $table->foreign('branch_code')->references('code')->on('branches')->onDelete('set null');
////            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
//
//            $table->softDeletes();
//            $table->timestamps();
//        });
//
//        // create the history table
//        Schema::dropIfExists('stsk_requests_history');
//        DB::unprepared("CREATE TABLE stsk_requests_history LIKE stsk_requests;");
//        // alter table
//        DB::unprepared("ALTER TABLE stsk_requests_history MODIFY COLUMN id int(11) NOT NULL,
//          DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
//          ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
//          ADD PRIMARY KEY (id, revision);");
//
//        DB::unprepared("DROP TRIGGER IF EXISTS stsk_requests_ai;");
//        DB::unprepared("DROP TRIGGER IF EXISTS stsk_requests_au;");
//        //create after insert trigger
//        DB::unprepared("CREATE TRIGGER stsk_requests_ai AFTER INSERT ON stsk_requests FOR EACH ROW
//         INSERT INTO stsk_requests_history SELECT 'insert', NULL, d.*
//         FROM stsk_requests AS d WHERE d.id = NEW.id;");
//
//        DB::unprepared("CREATE TRIGGER stsk_requests_au AFTER UPDATE ON stsk_requests FOR EACH ROW
//         INSERT INTO stsk_requests_history SELECT 'update', NULL, d.*
//         FROM stsk_requests AS d WHERE d.id = NEW.id;");
//
//        DB::unprepared("CREATE TRIGGER stsk_requests_ad AFTER DELETE ON stsk_requests FOR EACH ROW
//         INSERT INTO stsk_requests_history SELECT 'delete', NULL, d.*
//         FROM stsk_requests AS d WHERE d.id = OLD.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS stsk_requests_ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS stsk_requests_au;");
        Schema::dropIfExists('stsk_requests_history');
        Schema::dropIfExists('stsk_requests');
    }
}
