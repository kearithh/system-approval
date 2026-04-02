<?php

use App\Disposal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRequestNumberDisposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = Disposal::where('status', config('app.approve_status_approve'))->whereNull('deleted_at')->get();
        foreach ($data as $item) {

            $codeGenerate = generateCode('disposals', $item->company_id, $item->id, 'DPA');
            $item->code_increase = $codeGenerate['increase'];
            $item->code = $codeGenerate['newCode'];

            $item->save();

        }
    }
}
