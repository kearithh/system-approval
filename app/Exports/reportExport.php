<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class reportExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return User::all();
    // }

    use Exportable;

    protected $data;


    public function __construct(array $data)
    {
        $this->data = $data;
        // dd($data);
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            //"#",
            "Company",
            "Department",
            "Report Name",
            "Type",
            "Report",
            "Submited Report",
            "Not Submit",
            "Date"
        ];
    }

    public function registerEvents(): array
    {
        
        $styleArray = [
            'font' => [
                'name'  => 'Khmer OS Content',
                'size'  => 11,
            ]
        ];

        
        return [
            AfterSheet::class => function(AfterSheet $event) use ($styleArray)
            {
                $cellRange = 'A1:H1'; // All headers
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                
                $event->sheet->getDelegate()->getStyle('A2:H'.(count($this->data)+1))->ApplyFromArray($styleArray); //All data under header

            },
        ];
    }

}
