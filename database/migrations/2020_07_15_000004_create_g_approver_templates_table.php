<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGApproverTemplatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('g_approver_templates', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('request_id');

			$table->unsignedBigInteger('approver_id');
			$table->string('approver_name')->nullable();
			$table->string('approver_position')->nullable();
			$table->string('comment')->nullable();
			$table->json('attachments')->nullable()->comment("
			[
				[
					'org_name' => 'original file name',
					'src' => 'path to store file',
					'uploaded_at' => 'upload date'
				],
			]");

			$table->string('status')->default(config('app.pending'))->comment('status store as string key');
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();

			$table->timestamps();
//			$table->foreign('reference_id')->references('id')->on('re_references')->onDelete('cascade');
		});
        // create the history table
//        Schema::dropIfExists('g_approver_templates_histories');
//        DB::unprepared("CREATE TABLE g_approver_templates_histories LIKE g_approver_templates;");
//        // alter table
//        DB::unprepared("ALTER TABLE g_approvers_histories MODIFY COLUMN id int(11) NOT NULL,
//		    DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
//		    ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
//		    ADD PRIMARY KEY (id, revision);");
//
//        DB::unprepared("DROP TRIGGER IF EXISTS g_approver_templates_creating;");
//        DB::unprepared("DROP TRIGGER IF EXISTS g_approver_templates_updating;");
//        DB::unprepared("DROP TRIGGER IF EXISTS g_approver_templates_deleting;");
//
//        //create after insert trigger
//        DB::unprepared("CREATE TRIGGER g_approver_templates_creating AFTER INSERT ON g_approver_templates FOR EACH ROW
//			INSERT INTO g_approver_templates_histories SELECT 'insert', NULL, d.*
//			FROM g_approver_templates AS d WHERE d.id = NEW.id;");
//
//        DB::unprepared("CREATE TRIGGER g_approver_templates_updating AFTER UPDATE ON g_approver_templates FOR EACH ROW
//			 INSERT INTO g_approver_templates_histories SELECT 'update', NULL, d.*
//			 FROM g_approver_templates AS d WHERE d.id = NEW.id;");
//
//        DB::unprepared("CREATE TRIGGER g_approver_templates_deleting BEFORE DELETE ON g_approver_templates FOR EACH ROW
//			 INSERT INTO g_approver_templates_histories SELECT 'delete', NULL, d.*
//			 FROM g_approver_templates AS d WHERE d.id = OLD.id;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('g_approver_templates');
	}
}
