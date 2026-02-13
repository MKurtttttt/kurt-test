<?php

namespace App\Imports\Sheets\Users;

use App\Models\HiringInfo;
use App\Models\HiringHistory;
use App\Models\Departments;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HiringInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
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

    // Prepare row data before validation
    public function prepareForValidation($data, $index)
    {
        // Convert Excel date to proper format for validation
        if (isset($data['date_hired'])) {
            if ($data['date_hired'] instanceof \DateTime) {
                $data['date_hired'] = $data['date_hired']->format('m/d/Y');
            } elseif (is_numeric($data['date_hired'])) {
                try {
                    $date = Date::excelToDateTimeObject($data['date_hired']);
                    $data['date_hired'] = $date->format('m/d/Y');
                } catch (\Exception $e) {
                    // Keep original value if conversion fails
                }
            }
        }
        
        return $data;
    }

    public function rules(): array
    {
        return [
            'date_hired' => 'nullable|date_format:m/d/Y',
            'position' => 'nullable|in:Faculty,NTP',
            'nature' => 'nullable|in:Full-time,Part-time',
            'tenure' => 'nullable|in:Permanent,Probationary,Non-tenured',
            'non_tenured' => 'nullable|in:Fixed Term,GL,Contractual,Substitution',
            'required_license' => 'nullable|in:Yes,No',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'date_hired.date_format' => 'Hiring Info: Date hired must be in (MM/DD/YYYY) format',
            'position.in' => 'Hiring Info: Position must be Faculty or NTP',
            'nature.in' => 'Hiring Info: Nature must be Full-time or Part-time',
            'tenure.in' => 'Hiring Info: Tenure must be Permanent, Probationary, or Non-tenured',
            'non_tenured.in' => 'Hiring Info: Non-tenured must be Fixed Term, GL, Contractual, or Substitution',
            'required_license.in' => 'Hiring Info: Required License must be Yes or No',
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
        
        // Only validate as required for role not in rolesWithLimitedInfo
        if (in_array($role, $this->rolesWithLimitedInfo)) {
            return true; // Skip validation for roles in rolesWithLimitedInfo
        }

        $requiredFields = [
            'date_hired' => 'Date Hired',
            'position' => 'Position',
            'nature' => 'Nature',
            'tenure' => 'Tenure',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $failure = new Failure(
                    $excelRow,
                    $field,
                    ["Hiring Info: {$label} is required"]
                );
                $this->failures[] = $failure;
            }
        }

        // Special validation: If tenure is "Non-tenured", then non_tenured field is required
        if (!empty($row['tenure']) && strtolower($row['tenure']) == 'non-tenured') {
            if (empty($row['non_tenured'])) {
                $failure = new Failure(
                    $excelRow,
                    'non_tenured',
                    ["Hiring Info: Non Tenured type is required when Tenure is 'Non-tenured'"]
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
                    // With empty row: This is an ERROR - will be caught
                } else {
                    $processedRows[] = $excelRow;
                    continue;
                }
            } else {
                if (!in_array($role, $this->rolesWithLimitedInfo)) {
                    // role in rolesWithLimitedInfo with data: validate all required fields
                    $this->validateRequiredFields($row, $excelRow);
                }
            }
            
            $processedRows[] = $excelRow;

            // If validateCells, don't save
            if ($this->validateCells) {
                continue;
            }

            // Determine division based on position
            $div = "";
            if($row['position'] == 'Faculty')
            {
                $div = 'Academic';
            } 
            else 
            {
                $div = 'Non-Academic';
            }

            // If tenure is "Non-tenured", set non_tenured to the value, else null
            $nonTenured = null;
            if (strtolower($row['tenure']) == 'non-tenured') {
                $nonTenured = $row['non_tenured'];
            }

            // If required license is "Yes" or "1", set to 1, else 0
            $license = false;
            if (strtolower($row['required_license']) == 'yes' || $row['required_license'] == '1' || $row['required_license'] === 1) {
                $license = true;
            }

            // Create HiringInfo record
            $data = [
                'emp_id' => $this->rows[$excelRow]['emp_id'],
                'emp_position' => $row['position'],
                'emp_nature' => $row['nature'],
                'emp_tenure' => $row['tenure'],
                'non_tenured' => $nonTenured,
                'division' => $div,
                'license' => $license,
            ];

            HiringInfo::create($data);

            // Generate unique ID for HiringHistory
            do { 
                $uid = $this->rows[$excelRow]['emp_id'] . '-h-' . Str::random(8); 
            } while (HiringHistory::where('id', $uid)->exists()); 

            // Get department name from department code
            $deptName = Departments::where('code', $this->rows[$excelRow]['emp_dept'])->first();
            
            // If department not found, use the department code as fallback
            $department = $deptName ? $deptName->dept : $this->rows[$excelRow]['emp_dept'];

            // Convert Excel date to proper format
            $dateHired = null;
            if (!empty($row['date_hired'])) {
                if ($row['date_hired'] instanceof \DateTime) {
                    $dateHired = $row['date_hired']->format('Y-m-d');
                } 
                elseif (is_numeric($row['date_hired'])) {
                    $dateHired = Date::excelToDateTimeObject($row['date_hired'])->format('Y-m-d');
                } 
                else {
                    // Convert from MM/DD/YYYY to Y-m-d
                    try {
                        $dateHired = \DateTime::createFromFormat('m/d/Y', $row['date_hired'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dateHired = $row['date_hired'];
                    }
                }
            }

            // Create HiringHistory record
            HiringHistory::create([
                'id' => $uid,
                'emp_id' => $this->rows[$excelRow]['emp_id'],
                'date' => $dateHired,
                'position' => $row['position'],
                'division' => $div,
                'department' => $department,
                'nature' => $row['nature']
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
            
            // If role not in rolesWithLimitedInfo and Hiring Info is empty, this is an ERROR
            if (!in_array($role, $this->rolesWithLimitedInfo)) {
                $failure = new Failure(
                    $excelRow,
                    'hiring_info',
                    ["Hiring Info: Date Hired, Position, Nature, and Tenure are required. Row is completely empty."]
                );
                $this->failures[] = $failure;
            }
        }
    }
}