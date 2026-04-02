<?php

use App\RequestOT;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequestNumberOTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = RequestOT::where('status', config('app.approve_status_approve'))->whereNull('deleted_at')->get();
        foreach ($data as $item) {

            $codeGenerate = generateCode('request_ot', $item->company_id, $item->id, 'OT');
            $item->code_increase = $codeGenerate['increase'];
            $item->code = $codeGenerate['newCode'];

            $item->save();

        }
    }
}
