<?php

namespace App\Imports\Sheets\Users;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class AccountingDetailsSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
{
    protected $rows;
    protected $failures = [];
    protected $validateCells = false;
    protected $rolesWithLimitedInfo;
    protected $currentRow = 7;

    public function __construct(&$rows, $validateCells = false, $rolesWithLimitedInfo)
    {
        $this->rows = &$rows;
        $this->validateCells = $validateCells;
        $this->rolesWithLimitedInfo = $rolesWithLimitedInfo;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->currentRow = 7;
            },
        ];
    }

    public function headingRow(): int
    {
        return 7;
    }

    public function rules(): array
    {
        return [
            'sss_number' => 'nullable|max:255',
            'tax_identification_number' => 'nullable|max:255',
            'pag_ibig_number' => 'nullable|max:255',
            'philhealth_number' => 'nullable|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            // Messages handled in validateRequiredFields
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getFailures()
    {
        return $this->failures;
    }

    protected function validateRequiredFields($row, $excelRow)
    {
        $role = $this->rows[$excelRow]['role'] ?? null;
        
        if (in_array($role, $this->rolesWithLimitedInfo)) {
            return true;
        }

        $requiredFields = [
            'sss_number' => 'SSS Number',
            'tax_identification_number' => 'TIN',
            'pag_ibig_number' => 'Pag-IBIG Number',
            'philhealth_number' => 'PhilHealth Number',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $failure = new Failure(
                    $excelRow,
                    $field,
                    ["Accounting Details: {$label} is required"]
                );
                $this->failures[] = $failure;
            }
        }

        return true;
    }

    public function collection(Collection $collection)
    {
        $processedRows = []; // Track which rows we've processed
        
        // Process rows with data
        foreach ($collection as $index => $row) {
            $this->currentRow++;
            $excelRow = $this->currentRow;

            // Check if row is completely empty
            $isEmptyRow = true;
            foreach ($row as $value) {
                if ($value !== null && $value !== '') {
                    $isEmptyRow = false;
                    break;
                }
            }

            // Check if PersonalInfo exists
            if (!isset($this->rows[$excelRow]) || empty($this->rows[$excelRow]['emp_id'])) {
                continue; 
            }

            $role = $this->rows[$excelRow]['role'] ?? null;

            // Handle based on role and data presence
            if ($isEmptyRow) {
                // Row is empty
                if (!in_array($role, $this->rolesWithLimitedInfo)) {
                    continue;
                } else {
                    $processedRows[] = $excelRow;
                    continue;
                }
            } else {
                if (!in_array($role, $this->rolesWithLimitedInfo)) {
                    $this->validateRequiredFields($row, $excelRow);
                }
            }
            
            $processedRows[] = $excelRow;

            // If validateCells, don't save
            if ($this->validateCells) {
                continue;
            }

            DB::table('tbl_accounting_details')->insert([
                'emp_id' => $this->rows[$excelRow]['emp_id'],
                'sss_no' => $row['sss_number'],
                'tax_no' => $row['tax_identification_number'],
                'pagibig_no' => $row['pag_ibig_number'],
                'philhealth_no' => $row['philhealth_number'],
            ]);
        }
        
        foreach ($this->rows as $excelRow => $rowData) {
            // Skip if no emp_id
            if (empty($rowData['emp_id'])) {
                continue;
            }
            
            // Skip if already processed
            if (in_array($excelRow, $processedRows)) {
                continue;
            }
            
            // Get role
            $role = $rowData['role'] ?? null;
            
            // If role not in rolesWithLimitedInfo and Accounting Details is empty, this is an ERROR
            if (!in_array($role, $this->rolesWithLimitedInfo)) {
                $failure = new Failure(
                    $excelRow,
                    'accounting_details',
                    ["Accounting Details: SSS Number, TIN, Pag-IBIG Number, and PhilHealth Number are required. Row is completely empty."]
                );
                $this->failures[] = $failure;
            }
        }
    }
}