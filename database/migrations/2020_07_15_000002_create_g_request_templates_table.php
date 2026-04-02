<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGRequestTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_request_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('status')->default(config('app.draft'));
            $table->string('name');
            $table->string('reviewer');
            $table->string('tags')->nullable()->comment('daily,weekly,monthly,yearly');

            $table->json('properties')->nullable()->comment("
                [
                    '{key}' => '{value}'
                ]
            ");
            $table->json('attachments')->nullable()->comment("
            [
                [
                    'org_name' => 'original file name',
                    'src' => 'path to store file',
                    'uploaded_at' => 'upload date'
                ],
            ]");

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // create the history table
        Schema::dropIfExists('g_request_template_histories');
        DB::unprepared("CREATE TABLE g_request_template_histories LIKE g_request_templates;");
        // alter table
        DB::unprepared("ALTER TABLE g_request_template_histories MODIFY COLUMN id int(11) NOT NULL,
		    DROP PRIMARY KEY, ENGINE = MyISAM, ADD action VARCHAR(8) DEFAULT 'insert' FIRST,
		    ADD revision INT(6) NOT NULL AUTO_INCREMENT AFTER action,
		    ADD PRIMARY KEY (id, revision);");

        DB::unprepared("DROP TRIGGER IF EXISTS g_request_templates_creating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_request_templates_updating;");
        DB::unprepared("DROP TRIGGER IF EXISTS g_request_templates_deleting;");

        //create after insert trigger
        DB::unprepared("CREATE TRIGGER g_request_templates_creating AFTER INSERT ON g_request_templates FOR EACH ROW
			INSERT INTO g_request_template_histories SELECT 'insert', NULL, d.*
			FROM g_request_templates AS d WHERE d.id = NEW.id;");

        //create after update trigger
        DB::unprepared("CREATE TRIGGER g_request_templates_updating AFTER UPDATE ON g_request_templates FOR EACH ROW
			 INSERT INTO g_request_template_histories SELECT 'update', NULL, d.*
			 FROM g_request_templates AS d WHERE d.id = NEW.id;");

        //create before delete trigger
        DB::unprepared("CREATE TRIGGER g_request_templates_deleting BEFORE DELETE ON g_request_templates FOR EACH ROW
			 INSERT INTO g_request_template_histories SELECT 'delete', NULL, d.*
			 FROM g_request_templates AS d WHERE d.id = OLD.id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_request_templates');
    }
}
