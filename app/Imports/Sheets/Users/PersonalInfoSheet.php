<?php

namespace App\Imports\Sheets\Users;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PersonalInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents, SkipsEmptyRows
{
    protected $rows;
    protected $failures = [];
    protected $validateCells = false;
    protected $currentRow = 7;
    protected $rolesWithLimitedInfo;

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

    // Determine if a row is empty - this prevents validation on truly empty rows
    public function isEmptyWhen(array $row): bool
    {
        // Row is empty if employee_id is null/empty
        return empty($row['employee_id']);
    }

    // Prepare row data before validation
    public function prepareForValidation($data, $index)
    {
        // Convert Excel date to proper format for validation
        if (isset($data['date_of_birth'])) {
            if ($data['date_of_birth'] instanceof \DateTime) {
                $data['date_of_birth'] = $data['date_of_birth']->format('m/d/Y');
            } elseif (is_numeric($data['date_of_birth'])) {
                // Excel serial number
                try {
                    $date = Date::excelToDateTimeObject($data['date_of_birth']);
                    $data['date_of_birth'] = $date->format('m/d/Y');
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
            'employee_id' => [
                'required',
                'numeric',
                Rule::unique('tbl_info', 'emp_id'),
            ],
            'first_name' => 'required|max:255',
            'middle_name' => 'nullable|max:255',
            'last_name' => 'required|max:255',
            'department' => 'required|max:255',
            'gender' => 'nullable|in:Male,Female',
            'maiden_name' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date_format:m/d/Y',
            'place_of_birth' => 'nullable|max:255',
            'civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
            'religion' => 'nullable|max:255',
            'blood_type' => 'nullable|max:10',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'employee_id.required' => 'Personal Info: Employee ID is required',
            'employee_id.unique' => 'Personal Info: Employee ID :input already exists',
            'employee_id.numeric' => 'Personal Info: Employee ID must be a number',
            'first_name.required' => 'Personal Info: First name is required',
            'last_name.required' => 'Personal Info: Last name is required',
            'department.required' => 'Personal Info: Department is required',
            'gender.in' => 'Personal Info: Gender must be Male or Female',
            'date_of_birth.date_format' => 'Personal Info: Date of birth must be in MM/DD/YYYY format',
            'civil_status.in' => 'Personal Info: Civil status must be Single, Married, Widowed, or Separated',
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

    // Validate date of birth and place of birth based on role
    protected function validateRoleBasedFields($row, $excelRow, $role)
    {
        // If role in rolesWithLimitedInfo, date of birth and place of birth are optional
        if (in_array($role, $this->rolesWithLimitedInfo)) {
            return true;
        }

        // For role not in rolesWithLimitedInfo, validate date of birth and place of birth as required
        if (empty($row['date_of_birth'])) {
            $failure = new Failure(
                $excelRow,
                'date_of_birth',
                ["Personal Info: Date of birth is required"]
            );
            $this->failures[] = $failure;
        }

        if (empty($row['place_of_birth'])) {
            $failure = new Failure(
                $excelRow,
                'place_of_birth',
                ["Personal Info: Place of birth is required"]
            );
            $this->failures[] = $failure;
        }

        return true;
    }

    public function collection(Collection $collection)
    {
        // Store all rows without validation
        $tempRows = [];
        
        foreach ($collection as $index => $row) 
        {
            $this->currentRow++;
            $excelRow = $this->currentRow;

            // Convert date if needed
            $dateOfBirth = $row['date_of_birth'] ?? null;
            if ($dateOfBirth instanceof \DateTime) {
                $dateOfBirth = $dateOfBirth->format('Y-m-d');
            } elseif (is_numeric($dateOfBirth)) {
                try {
                    $dateOfBirth = Date::excelToDateTimeObject($dateOfBirth)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Keep original if conversion fails
                }
            } else if ($dateOfBirth) {
                // If it's a string in m/d/Y format, convert to Y-m-d
                try {
                    $dateOfBirth = \DateTime::createFromFormat('m/d/Y', $dateOfBirth)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Keep original if conversion fails
                }
            }
            
            // Store row data for other sheets to access
            // Key is the actual Excel row number
            $this->rows[$excelRow] = [
                'emp_id'  => $row['employee_id'],
                'emp_fname' => $row['first_name'],
                'emp_mname'  => $row['middle_name'],
                'emp_lname' => $row['last_name'],
                'emp_dept'  => $row['department'],
                'emp_gender' => $row['gender'],
                'emp_maiden_name'  => $row['maiden_name'],
                'emp_dob' => $dateOfBirth,
                'emp_pob'  => $row['place_of_birth'],
                'emp_cStatus' => $row['civil_status'],
                'emp_religion'  => $row['religion'],
                'emp_blood_type' => $row['blood_type'],
            ];
            
            // Store temporary data with original row for later validation
            $tempRows[$excelRow] = $row;
        }
        
        // After LoginInfo has been processed, validate role-based fields
        // This will be called after LoginInfo sheet stores the role
        if (!$this->validateCells) {
            return; // Skip validation in non-dry-run mode
        }
    }
    
    // Validate personal info fields after role is known
    public function validateAfterRoleAssignment()
    {
        foreach ($this->rows as $excelRow => $rowData) {
            // Skip if no emp_id
            if (empty($rowData['emp_id'])) {
                continue;
            }
            
            // Get role 
            $role = $rowData['role'] ?? null;
            
            // If no role assigned yet, then no validation
            if (!$role) {
                continue;
            }
            
            // Validate date of birth and place of birth based on role
            if (!in_array($role, $this->rolesWithLimitedInfo)) {
                if (empty($rowData['emp_dob'])) {
                    $failure = new Failure(
                        $excelRow,
                        'date_of_birth',
                        ["Personal Info: Date of birth is required"]
                    );
                    $this->failures[] = $failure;
                }
                
                if (empty($rowData['emp_pob'])) {
                    $failure = new Failure(
                        $excelRow,
                        'place_of_birth',
                        ["Personal Info: Place of birth is required"]
                    );
                    $this->failures[] = $failure;
                }
            }
        }
    }
}