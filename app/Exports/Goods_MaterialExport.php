<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Stock\Unit;
// use Maatwebsite\Excel\Concerns\FromQuery;
use App\Models\Stock\Category;
use App\Models\Stock\Supplier;
use Illuminate\Contracts\View\View;
use App\Models\Stock\Goods_material;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class Goods_MaterialExport implements 
// FromCollection, 
WithCustomStartCell,
ShouldAutoSize,
WithStyles,
WithMapping,
WithDrawings,
WithHeadings,
WithEvents,
// FromQuery,
WithColumnWidths,
FromView
{
    use Exportable;
    protected $data;
    protected $supplier_id;
    // protected $supplier_id;

    public function __construct(int $supplier_id, Array $data)
    {
        $this->data = $data;
        $this->supplier_id = $supplier_id;
    
    }
    // public function __construct(int $supplier_id)
    // {
    //     $this->supplier_id = $supplier_id;
    // }

    public function view(): View
    {
        // $good_materials = Goods_material::with('unit')
        // ->where('supplier_id', '=', $this->supplier_id)
        // ->where('required_qty', '>', 0)
        // ->orderBy('category_id','asc')
        // ->get();
        // $coa = DB::table('coas')->where('name', '=', $request->coa_name)->first();

        $c = Category::all('id','name');

        $categoryOptions = [];
        foreach ($c as  $sc) {
            $categoryOptions[$sc['id']] = $sc['name'];
        }
        // dd($categoryOptions);

        $u = Unit::all('id','name');

        $unitOptions = [];
        foreach ($u as  $su) {
            $unitOptions[$su['id']] = $su['name'];
        }
        // dd($unitOptions);
        
        $supplier = Supplier::find($this->supplier_id);
        $dateTimeInBrisbane = Carbon::now('Australia/Brisbane')->isoFormat('dddd D/M/Y, h:mm:ss a');
        // dd($good_materials);
        return view('exports.supplierOrder', [
            'good_materials' => $this->data,
            'date' => $dateTimeInBrisbane ,
            'supplier' =>  $supplier,
            'categoryOptions'=>$categoryOptions,
            'unitOptions'=>$unitOptions,
        ]);
        // return view('exports.supplierOrder', [
        //     'good_materials' => $good_materials,
        //     'date' => $dateTimeInBrisbane ,
        //     'supplier' =>  $supplier,
        // ]);
    }
    
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
        return Goods_material::query()->with('unit')->where('Price','>',12);;
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
            8    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FFA0A0A0',
                    ],
                    'endColor' => [
                        'argb' => 'FFFFFFFF',
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ],
            
            ],

            5 => [
                'font' => [
                    'color' => ['argb' => 'FFFF0000'],
                    'italic' => true,
                ],

            ],

            6 => [
               
                'font' => [
                    'color' => ['argb' => 'FFFF0000'],
                    'italic' => true,
                ],
            ],
          
            'A8:F8' => [
                'font' => [
                    'size' => 14,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        // 'color' => ['argb' => 'FFFF0000'],
                    ],
                   
                    'inside' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        // 'color' => ['argb' => 'FFFF0000'],
                    ],
                ],
            ],

            'A8:F100' => [
                'font' => [
                    'size' => 14,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        // 'color' => ['argb' => 'FFFF0000'],
                    ],
                   
                    'inside' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        // 'color' => ['argb' => 'FFFF0000'],
                    ],
                    
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],

                // 'alignment' => [
                //     'wrapText' => true,
                // ],          
               
            ],
            'A8:A100'=> [
               
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
               
            ],

            'C8:C100'  => [
                'font' => [
                'size' => 15,
                'bold' => true,
                'color' => ['argb' => 'FFFF0000'],                   
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'D8:E100'  => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],

            




            // // Styling a specific cell by coordinate.
            // 'B9' => ['font' => ['italic' => true]],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 26]],
        ];
    }

    public function map($good_material): array
    {
        return [
            $good_material->id,
            $good_material->name,
            $good_material->required_qty,
            $good_material->unit->name,
            $good_material->price
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A8:D12')->applyFromArray([
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
        $drawing->setPath(public_path('/img/logo_backend.jpg'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('H2');

        // $drawing2 = new Drawing();
        // $drawing2->setName('Other image');
        // $drawing2->setDescription('This is a second image');
        // $drawing2->setCoordinates('G2');

        // return [$drawing, $drawing2];
        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5           
        ];
    }


}
