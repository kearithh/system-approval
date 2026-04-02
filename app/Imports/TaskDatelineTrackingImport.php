<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Model\ContractMagement\TaskDatelineTracking;

class TaskDatelineTrackingImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row)
        {
            try {
                //code...
                if(!(trim($row[0])) || !(trim($row[1])) || !(trim($row[2]))){
                    continue;
                }

                $date = Date::excelToDateTimeObject($row[0])->format('d-m-Y');
                $data = [
                    'created_by'    => Auth::id(),
                    'data' => json_encode([
                        'due_date'          => $date,
                        'description'       => $row[1],
                        'is_id_telegram'    => $row[2],
                    ]),

                ];
               TaskDatelineTracking::create($data);

            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
   public function dateFormat($date)
    {
        $m = preg_replace('/[^0-9]/', '', $date);
        if (preg_match_all('/\d{2}+/', $m, $r)) {
            $r = reset($r);
            if (count($r) == 4) {
                if ($r[2] <= 12 && $r[3] <= 31) return "$r[0]$r[1]-$r[2]-$r[3]"; // Y-m-d
                if ($r[0] <= 31 && $r[1] != 0 && $r[1] <= 12) return "$r[2]$r[3]-$r[1]-$r[0]"; // d-m-Y
                if ($r[0] <= 12 && $r[1] <= 31) return "$r[2]$r[3]-$r[0]-$r[1]"; // m-d-Y
                if ($r[2] <= 31 && $r[3] <= 12) return "$r[0]$r[1]-$r[3]-$r[2]"; //Y-m-d
            }

            $y = $r[2] >= 0 && $r[2] <= date('y') ? date('y') . $r[2] : (date('y') - 1) . $r[2];
            if ($r[0] <= 31 && $r[1] != 0 && $r[1] <= 12) return "$y-$r[1]-$r[0]"; // d-m-y
        }
    }
}
