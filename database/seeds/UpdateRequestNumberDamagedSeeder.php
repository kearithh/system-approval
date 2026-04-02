<?php

use App\DamagedLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequestNumberDamagedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = DamagedLog::where('status', config('app.approve_status_approve'))->whereNull('deleted_at')->get();
        foreach ($data as $item) {

            $codeGenerate = generateCode('damaged_log', $item->company_id, $item->id, 'DMA');
            $item->code_increase = $codeGenerate['increase'];
            $item->code = $codeGenerate['newCode'];

            $item->save();

        }
    }
}
