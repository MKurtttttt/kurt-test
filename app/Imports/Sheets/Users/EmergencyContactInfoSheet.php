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

class EmergencyContactInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
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
            'first_name' => 'nullable|max:255',
            'middle_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'relationship' => 'nullable|max:255',
            'house_number' => 'nullable|max:255',
            'street' => 'nullable|max:255',
            'city' => 'nullable|max:255',
            'province' => 'nullable|max:255',
            'postal_code' => 'nullable|max:255',
            'home_phone' => 'nullable|max:255',
            'mobile_phone' => 'nullable|max:255',
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
        
        // Only validate as required for roles not in rolesWithLimitedInfo
        if (in_array($role, $this->rolesWithLimitedInfo)) {
            return true; // Skip validation for roles in rolesWithLimitedInfo
        }

        // For not in rolesWithLimitedInfo: all fields required EXCEPT middle_name
        $requiredFields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'relationship' => 'Relationship',
            'house_number' => 'House Number',
            'street' => 'Street',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => 'Postal Code',
            'home_phone' => 'Home Phone',
            'mobile_phone' => 'Mobile Phone',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $failure = new Failure(
                    $excelRow,
                    $field,
                    ["Emergency Contact Info: {$label} is required"]
                );
                $this->failures[] = $failure;
            }
        }

        return true;
    }

    public function collection(Collection $collection)
    {
        $processedRows = []; // Track which rows were processed
        
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

            // If validateCells don't save
            if ($this->validateCells) {
                continue;
            }

            DB::table('tbl_emergency')->insert([
                'emp_id' => $this->rows[$excelRow]['emp_id'],
                'cp_fname' => $row['first_name'],
                'cp_mname' => $row['middle_name'],
                'cp_lname' => $row['last_name'],
                'cp_relationship' => $row['relationship'],
                'cp_house_no' => $row['house_number'],
                'cp_street' => $row['street'],
                'cp_city' => $row['city'],
                'cp_province' => $row['province'],
                'cp_postal_code' => $row['postal_code'],
                'cp_home_phone' => $row['home_phone'],
                'cp_mobile_no' => $row['mobile_phone'],
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
            
            // If role not in rolesWithLimitedInfo and Emergency Contact is empty, this is an ERROR
            if (!in_array($role, $this->rolesWithLimitedInfo)) {
                $failure = new Failure(
                    $excelRow,
                    'emergency_contact',
                    ["Emergency Contact Info: All fields (except Middle Name) are required. Row is completely empty."]
                );
                $this->failures[] = $failure;
            }
        }
    }
}