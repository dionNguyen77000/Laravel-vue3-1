<?php

namespace App\Exports;

use App\Models\Stock\Goods_material;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class Goods_MaterialExport implements 
// FromCollection, 
WithCustomStartCell,
ShouldAutoSize,
WithStyles,
// WithMapping,
WithDrawings,
WithHeadings,
WithEvents,
FromQuery
{
    use Exportable;
    protected $data;

    // public function headings():array{
    //     return [
    //         'id',
    //         'name'
    //     ];
    // }
    
    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Slug',
            'Price'
        ];
    }
    public function query()
    {
        return Goods_material::query()->where('Price','>',12);;
        // ->where('Preparation','=',$request->Preparation);
        // return collect(Goods_material::)
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            8    => ['font' => ['bold' => true]],

            // // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    // public function map($good_material): array
    // {
    //     return [
    //         $good_material->id,
    //         $good_material->name,
    //         $good_material->slug,
    //         $good_material->price,
    //     ];
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A8:D8')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]
                ]);
            },
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/img/logo_backend.png'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('B2');

        return $drawing;
    }



}
