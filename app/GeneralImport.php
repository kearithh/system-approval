<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class GeneralImport implements ToCollection, WithCalculatedFormulas
{
    public $id;
    public $data;

    public function collection(Collection $rows)
    {
        $data = @$rows;
        $this->data = $data;

        unset($rows[0]);

        $usd = $rows->where('5', 'USD')->sum('7');
        $khr = $rows->where('5', 'KHR')->sum('7');

        $request = New RequestHR();
            $request->user_id = Auth::id();
            $request->created_by = Auth::id();
            $request->total = $usd;
            $request->total_khr = $khr;
            $request->remark = null;
            $request->import = 'Yes';
            $request->att_name = null;
            $request->attachment = null;
            $request->status = config('app.approve_status_draft');
            $request->company_id = Auth::user()->company_id;

        if($request->save()){

            $id = $request->id;

            foreach ($rows as $row)
            {

                $last_purchase_date = (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]));

                DB::table('request_hr_items')->insert([
                    'request_id' => $id,
                    'name' => null,
                    'desc' => $row[1],
                    'purpose' => $row[2],
                    'unit' => $row[3],
                    'qty' => $row[4],
                    'currency' => $row[5],
                    'unit_price' => $row[6],
                    'account_no' => $row[10],
                    'balance' => $row[11],
                    'remark' => $row[12],
                    'last_purchase_date' => $last_purchase_date,
                    'remain_qty' => $row[9],
                    'import' => 'Yes',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->id = $id;
                    
            }
        }
        else{
            
        }
    }
}
