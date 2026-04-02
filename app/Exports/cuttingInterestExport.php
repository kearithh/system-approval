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

class cuttingInterestExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
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
            "#",
            "ស្ថានភាព",
            "ក្រុមហ៊ុន",
            "សាខា",
            "អតិថិជន",
            "CID",
            "ប្រាក់ដើមជំពាក់នៅសល់",
            "ការប្រាក់ជំពាក់នៅសល់",
            "ការប្រាក់ហួសកាលកំណត់",
            "ប្រាក់ពិន័យពេលបង់ផ្តាច់",
            "សេវារដ្ឋបាលជំពាក់នៅសល់",
            "ប្រាក់ពិន័យយឺតយ៉ាវ",
            "ប្រាក់ត្រូវបង់សរុប",
            "ប្រាក់ស្នើរសុំកាត់",
            "ប្រាក់អតិថិជនព្រៀមព្រៀងបង់",
            "ប្រាក់ដើមយកបាន",
            "ការប្រាក់ជំពាក់យកបាន",
            "ការប្រាក់ហួសកាលកំណត់យកបាន",
            "សេវារដ្ឋបាលយកបាន",
            "ប្រាក់ពិន័យយកបាន",
            "រយៈពេលខ្ចី(ខែ)",
            "ប្រភេទកម្ចី",
            "ចំនួនថ្ងៃយឺត(ថ្ងៃ)",
            "កម្មវត្ថុ",
            "មូលហេតុ",
            "ស្នើរដោយ",
            "កាលបរិច្ឆេទ",
        ];
    }

    public function registerEvents(): array
    {
        
        $styleHeader = [
            'font' => [
                'name'  => 'Khmer OS Content',
                'size'  => 11,
                'bold'  =>  true,
            ]
        ];

        $styleData = [
            'font' => [
                'name'  => 'Khmer OS Content',
                'size'  => 10,
            ]
        ];

        
        return [
            AfterSheet::class => function(AfterSheet $event) use ($styleHeader, $styleData)
            {
                $cellRange = 'A1:AA1'; // All headers
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleHeader);
                $event->sheet->getDelegate()->getStyle($cellRange);
                
                $event->sheet->getDelegate()->getStyle('A2:AA'.(count($this->data)+1))->ApplyFromArray($styleData); //All data under header

            },
        ];
    }

}
