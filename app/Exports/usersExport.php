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

class usersExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
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
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            // "#",
            "ស្ថានភាព",
            "បានប្រើ/មិនទាន់ប្រើ",
            "គោត្តនាម និងនាម",
            "អ្នកប្រើប្រាស់",
            "លេខសំគាល់បុគ្គលិក",
            "មុខតំណែង",
            "សាខា",
            "ក្រុមហ៊ុន",
            "ហត្ថលេខា",
            "ហត្ថលេខាតូច",
            "អ៊ីម៉ែល"
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
                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                
                $event->sheet->getDelegate()->getStyle('A2:L'.(count($this->data)+1))->ApplyFromArray($styleArray); //All data under header
            },
        ];
    }

}
