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

class loanExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
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
            "ល.រ",
            "ស្ថានភាព",
            "កាលបរិច្ឆេទស្នើរ",
            "កាលបរិច្ឆេទកែសម្រួល",
            "ស្នើរដោយ",
            "អនុម័តដោយ",
            "ក្រុមហ៊ុន",
            "ឈ្មោះសាខា",
            "ឈ្មោះមន្រ្តីឥណទាន",
            "ឈ្មោះអ្នកខ្ចី",
            "ឈ្មោះអ្នករួមខ្ចី",
            "ទំហំឥណទាន(រៀល)",
            "រយៈពេលខ្ចី(ខែ)",
            "របៀបសង",
            "អត្រាការប្រាក់(%)",
            "សេវារដ្ឋបាល(%)",
            "សេវារៀបចំឥណទាន(%)",
            "សេវាត្រួតពិនិត្យឥណទាន(%)",
            "សេវាប្រមូលឥណទាន(%)",
            "របៀបអនុម័ត",
            "ប្រភេទឥណទាន",
            "តំណភ្ជាប់ទីតាំង(#Map1)",
            "តំណភ្ជាប់ទីតាំង(#Map2)",
            "តំណភ្ជាប់ទីតាំង(#Map3)",
            "តំណភ្ជាប់ទីតាំង(#Map4)",
            "តំណភ្ជាប់ទីតាំង(#Map5)"
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
                $cellRange = 'A1:z1'; // All headers
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                
                $event->sheet->getDelegate()->getStyle('A2:z'.(count($this->data)+1))->ApplyFromArray($styleArray); //All data under header

            },
        ];
    }


}
