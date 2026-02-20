<?php

namespace App\Exports\Sheets;

use App\Models\Kpi;

use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiSheet implements WithTitle, WithEvents
{
    protected $kpi;
    protected $title;
    protected $fromList;

    public function __construct(Kpi $kpi, bool $fromList = false)
    {
        $this->kpi = $kpi->load(['segmentations', 'accreditations', 'dimensions']);
        $this->title = $kpi->measure_code;
        $this->fromList = $fromList;
    }
    
    public function registerEvents(): array
    {
        Sheet::macro('styleHeading', function (Sheet $sheet, string $cellRange) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '977C01']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DEDEDE'],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '808080'],
                    ],
                ],
            ]);
        });

        Sheet::macro('styleContent', function (Sheet $sheet, string $cellRange) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '808080'],
                    ],
                ],
            ]);
        });

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                            
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setWidth(30);
                }
                foreach (range(2,19) as $row){
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                if ($this->fromList) {
                    $sheet->setCellValue('A1', '<== Go back to KPI List');
                    $sheet->getCell('A1')->getHyperlink()->setURL("sheet://'KPI List'!A1")->setTooltip("Go to KPI List");
                    $sheet->getStyle('A1')->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => '0563C1'],
                            'underline' => 'single',
                        ],
                    ]);
                }

                //Header
                $sheet->mergeCells('A2:E3');
                $sheet->setCellValue(
                    'A2',
                    "HOLY ANGEL UNIVERSITY\nBALANCED SCORECARD Performance Measures Information Card"
                );
                $event->sheet->styleHeading('A2:E3');

                //Perspective
                $event->sheet->styleHeading('A4');
                $sheet->setCellValue('A4', "Perspective");
                $event->sheet->styleContent('A5');
                $sheet->setCellValue('A5', $this->kpi->perspective);
                
                //Measure Code
                $event->sheet->styleHeading('B4');
                $sheet->setCellValue('B4', "Measure Code");
                $event->sheet->styleContent('C4');
                $sheet->setCellValue('C4', $this->kpi->measure_code);

                //Measure Owner
                $event->sheet->styleHeading('D4');
                $sheet->setCellValue('D4', "Measure Owner");
                $event->sheet->styleContent('E4');
                $sheet->setCellValue('E4', $this->kpi->measure_owner);

                //Measure Name
                $event->sheet->styleHeading('B5');
                $sheet->setCellValue('B5', "Measure Name");
                $sheet->mergeCells('C5:E5');
                $event->sheet->styleContent('C5:E5');
                $sheet->setCellValue('C5', $this->kpi->measure_name);

                //Strategic Theme
                $event->sheet->styleHeading('A6');
                $sheet->setCellValue('A6', "Strategic Theme");
                $sheet->getRowDimension(7)->setRowHeight(120);
                $event->sheet->styleContent('A7');
                $sheet->getStyle('A7')->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '800000'],
                    ],
                ]);
                $sheet->setCellValue('A7', $this->kpi->strategic_theme);
                
                //Description
                $sheet->mergeCells('B6:B7');
                $event->sheet->styleHeading('B6:B7');
                $sheet->setCellValue('B6', "Description");
                $sheet->mergeCells('C6:E7');
                $event->sheet->styleContent('C6:E7');
                $sheet->setCellValue('C6', $this->kpi->description);

                //Objective
                $event->sheet->styleHeading('A8');
                $sheet->setCellValue('A8', "Objective");
                $sheet->getRowDimension(9)->setRowHeight(80);
                $event->sheet->styleContent('A9');
                $sheet->setCellValue('A9', $this->kpi->objective);

                //Measure Type
                $event->sheet->styleHeading('B8');
                $sheet->setCellValue('B8', "Measure Code");
                $event->sheet->styleContent('C8');
                $sheet->setCellValue('C8', $this->kpi->measure_type);

                //Lead/Lag
                $event->sheet->styleHeading('D8');
                $sheet->setCellValue('D8', "Measure Owner");
                $event->sheet->styleContent('E8');
                $sheet->setCellValue('E8', $this->kpi->lead_lag);

                //Objective Owner
                $event->sheet->styleHeading('A10');
                $sheet->setCellValue('A10', "Objective Owner");
                $event->sheet->styleContent('A11');
                $sheet->setCellValue('A11', $this->kpi->objective_owner);

                //Formula
                $sheet->mergeCells('B9:B10');
                $event->sheet->styleHeading('B9:B10');
                $sheet->setCellValue('B9', "Formula");
                $sheet->mergeCells('C9:E10');
                $event->sheet->styleContent('C9:E10');
                $sheet->setCellValue('C9', $this->kpi->formula);

                //Unit Type
                $event->sheet->styleHeading('B11');
                $sheet->setCellValue('B11', "Unit Type");
                $event->sheet->styleContent('C11');
                $sheet->setCellValue('C11', $this->kpi->unit_type);

                //Polarity
                $event->sheet->styleHeading('D11');
                $sheet->setCellValue('D11', "Polarity");
                $event->sheet->styleContent('E11');
                $sheet->setCellValue('E11', $this->kpi->polarity);

                //Objective Intended Results
                $event->sheet->styleHeading('A12');
                $sheet->setCellValue('A12', "Objective Intended Results");
                $sheet->mergeCells('A13:A15');
                foreach (range(12,15) as $row){
                    $sheet->getRowDimension($row)->setRowHeight(40);
                }
                $event->sheet->styleContent('A13:A15');
                $sheet->setCellValue('A13', $this->kpi->intended_results);

                //Data Provider
                $event->sheet->styleHeading('B12');
                $sheet->setCellValue('B12', "Data Provider");
                $event->sheet->styleContent('C12');
                $sheet->setCellValue('C12', $this->kpi->data_provider);

                //Data Source
                $event->sheet->styleHeading('D12');
                $sheet->setCellValue('D12', "Data Source");
                $event->sheet->styleContent('E12');
                $sheet->setCellValue('E12', $this->kpi->data_source);

                //Collection Frequency
                $event->sheet->styleHeading('B13');
                $sheet->setCellValue('B13', "Collection Frequency");
                $event->sheet->styleContent('C13');
                $sheet->setCellValue('C13', $this->kpi->collection_frequency);

                //Reporting Frequency
                $event->sheet->styleHeading('D13');
                $sheet->setCellValue('D13', "Reporting Frequency");
                $event->sheet->styleContent('E13');
                $sheet->setCellValue('E13', $this->kpi->reporting_frequency);

                //Verified By
                $event->sheet->styleHeading('B14');
                $sheet->setCellValue('B14', "Verified By");
                $event->sheet->styleContent('C14');
                $sheet->setCellValue('C14', $this->kpi->verified_by);

                //Validated by
                $event->sheet->styleHeading('D14');
                $sheet->setCellValue('D14', "Validated By");
                $event->sheet->styleContent('E14');
                $sheet->setCellValue('E14', $this->kpi->validated_by);

                //Baseline
                $event->sheet->styleHeading('B15');
                $sheet->setCellValue('B15', "Baseline");
                $sheet->mergeCells('C15:E15');
                $event->sheet->styleContent('C15:E15');
                $sheet->setCellValue('C15', $this->kpi->baseline);

                // Strategic Initiatives/Action Plans
                $event->sheet->styleHeading('A16');
                $sheet->setCellValue('A16', "Strategic Initiatives/Action Plans");
                $sheet->mergeCells('A17:A19');
                foreach (range(17,19) as $row){
                    $sheet->getRowDimension($row)->setRowHeight(40);
                }
                $event->sheet->styleContent('A17:A19');
                $sheet->setCellValue('A17', $this->kpi->strategic_initiatives);
                
                //Baseline
                $event->sheet->styleHeading('B16');
                $sheet->setCellValue('B16', "Target");
                $sheet->mergeCells('C16:E16');
                $event->sheet->styleContent('C16:E16');
                $sheet->setCellValue('C16', $this->kpi->target);

                //Threshold
                $event->sheet->styleHeading('B17');
                $sheet->setCellValue('B17', "Threshold");
                $sheet->mergeCells('C17:E17');
                $event->sheet->styleContent('C17:E17');
                $sheet->setCellValue('C17', 'Low: ' . $this->kpi->threshold_low . ' High: ' . $this->kpi->threshold_high);

                //Target Rationale
                $event->sheet->styleHeading('B18');
                $sheet->setCellValue('B18', "Target Rationale");
                $sheet->mergeCells('C18:E18');
                $event->sheet->styleContent('C18:E18');
                $sheet->setCellValue('C18', $this->kpi->target_rationale);

                //Comparator
                $event->sheet->styleHeading('B19');
                $sheet->setCellValue('B19', "Comparator");
                $sheet->mergeCells('C19:E19');
                $event->sheet->styleContent('C19:E19');
                $sheet->setCellValue('C19', $this->kpi->comparator);

                $currentRow = 20;
                
                // Add segmentations if they exist
                if ($this->kpi->segmentations && $this->kpi->segmentations->count() > 0) {
                    $currentRow = $this->addSegmentations($event->sheet, $sheet, $currentRow);
                }
                
                // Add accreditations if they exist
                if ($this->kpi->accreditations && $this->kpi->accreditations->count() > 0) {
                    $currentRow = $this->addAccreditations($event->sheet, $sheet, $currentRow);
                }
                
                // Add dimensions if they exist
                if ($this->kpi->dimensions && $this->kpi->dimensions->count() > 0) {
                    $currentRow = $this->addDimensions($event->sheet, $sheet, $currentRow);
                }
            }
        ];
    }

    protected function addSegmentations($eventSheet, $sheet, $startRow)
    {
        $currentRow = $startRow;
        
        // Add spacing
        $currentRow++;
        
        // Check if collection is empty
        if ($this->kpi->segmentations->isEmpty()) {
            return $currentRow;
        }
        
        // Get all columns and filter out unwanted ones
        $allColumns = array_keys($this->kpi->segmentations->first()->toArray());
        $excludeColumns = ['id', 'kpi_id', 'created_at', 'updated_at'];
        $columns = array_diff($allColumns, $excludeColumns);
        
        // Filter out columns that have no data in any record
        $columns = array_filter($columns, function($column) {
            return $this->kpi->segmentations->some(function($segmentation) use ($column) {
                $value = $segmentation->$column;
                return !is_null($value) && $value !== '';
            });
        });
        
        // Reset array keys
        $columns = array_values($columns);
        
        $lastCol = chr(65 + count($columns) - 1);
        
        // Add column headers dynamically
        foreach ($columns as $index => $column) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}{$currentRow}", ucwords(str_replace('_', ' ', $column)));
            $eventSheet->styleHeading("{$col}{$currentRow}");
        }
        
        // Add border to header row
        $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $currentRow++;
        
        // Add segmentation data
        foreach ($this->kpi->segmentations as $segmentation) {
            $data = $segmentation->toArray();
            
            foreach ($columns as $index => $column) {
                $col = chr(65 + $index);
                $sheet->setCellValue("{$col}{$currentRow}", $data[$column] ?? '');
            }
            
            // Style all columns
            $eventSheet->styleContent("A{$currentRow}:{$lastCol}{$currentRow}");
            
            // Add border to data row
            $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($currentRow)->setRowHeight(60);
            
            $currentRow++;
        }
        
        return $currentRow;
    }

    protected function addAccreditations($eventSheet, $sheet, $startRow)
    {
        $currentRow = $startRow;
        
        // Add spacing
        $currentRow++;
        
        if ($this->kpi->accreditations->isEmpty()) {
            return $currentRow;
        }
        
        // Filter out unwanted columns
        $allColumns = array_keys($this->kpi->accreditations->first()->toArray());
        $excludeColumns = ['id', 'kpi_id', 'created_at', 'updated_at'];
        $columns = array_diff($allColumns, $excludeColumns);
        
        // Filter out columns with no data
        $columns = array_filter($columns, function($column) {
            return $this->kpi->accreditations->some(function($accreditation) use ($column) {
                $value = $accreditation->$column;
                return !is_null($value) && $value !== '';
            });
        });
        
        $columns = array_values($columns);
        $lastCol = chr(65 + count($columns) - 1);
        
        // Add column headers dynamically
        foreach ($columns as $index => $column) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}{$currentRow}", ucwords(str_replace('_', ' ', $column)));
            $eventSheet->styleHeading("{$col}{$currentRow}");
        }
        
        // Add border to header row
        $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $currentRow++;
        
        // Add accreditation data
        foreach ($this->kpi->accreditations as $accreditation) {
            $data = $accreditation->toArray();
            
            foreach ($columns as $index => $column) {
                $col = chr(65 + $index);
                $sheet->setCellValue("{$col}{$currentRow}", $data[$column] ?? '');
            }
            
            $eventSheet->styleContent("A{$currentRow}:{$lastCol}{$currentRow}");
            
            // Add border to data row
            $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($currentRow)->setRowHeight(60);
            
            $currentRow++;
        }
        
        return $currentRow;
    }

    protected function addDimensions($eventSheet, $sheet, $startRow)
    {
        $currentRow = $startRow;
        
        // Add spacing
        $currentRow++;
            
        if ($this->kpi->dimensions->isEmpty()) {
            return $currentRow;
        }
        
        // Filter out unwanted columns
        $allColumns = array_keys($this->kpi->dimensions->first()->toArray());
        $excludeColumns = ['id', 'kpi_id', 'created_at', 'updated_at'];
        $columns = array_diff($allColumns, $excludeColumns);
        
        // Filter out columns with no data
        $columns = array_filter($columns, function($column) {
            return $this->kpi->dimensions->some(function($dimension) use ($column) {
                $value = $dimension->$column;
                return !is_null($value) && $value !== '';
            });
        });
        
        $columns = array_values($columns);
        $lastCol = chr(65 + count($columns) - 1);
        
        // Add column headers dynamically
        foreach ($columns as $index => $column) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}{$currentRow}", ucwords(str_replace('_', ' ', $column)));
            $eventSheet->styleHeading("{$col}{$currentRow}");
        }
        
        // Add border to header row
        $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $currentRow++;
        
        // Add dimension data
        foreach ($this->kpi->dimensions as $dimension) {
            $data = $dimension->toArray();
            
            foreach ($columns as $index => $column) {
                $col = chr(65 + $index);
                $sheet->setCellValue("{$col}{$currentRow}", $data[$column] ?? '');
            }
            
            $eventSheet->styleContent("A{$currentRow}:{$lastCol}{$currentRow}");
            
            // Add border to data row
            $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($currentRow)->setRowHeight(60);
            
            $currentRow++;
        }
        
        return $currentRow;
    }
    
    public function title(): string 
    {
        return $this->title;
    }

}
