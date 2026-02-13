<?php

namespace App\Imports\Sheets\Users;

use App\Models\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class ContactInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
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
            'house_number' => 'nullable|max:255',
            'street' => 'nullable|max:255',
            'barangay' => 'nullable|max:255',
            'city' => 'nullable|max:255',
            'province' => 'nullable|max:255',
            'postal_code' => 'nullable|max:255',
            'home_phone' => 'nullable|max:255',
            'mobile_phone' => 'nullable|max:255',
            'email_address_1' => 'nullable|email|max:255',
            'email_address_2' => 'nullable|email|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email_address_1.email' => 'Contact Info: Email Address 1 must be valid',
            'email_address_2.email' => 'Contact Info: Email Address 2 must be valid',
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

    // Custom validation after role is known
    protected function validateRequiredFields($row, $excelRow)
    {
        $role = $this->rows[$excelRow]['role'] ?? null;
        
        if (in_array($role, $this->rolesWithLimitedInfo)) {
            return true; // Skip validation for other roles
        }

        // Check required fields
        $requiredFields = [
            'house_number' => 'House Number',
            'street' => 'Street',
            'barangay' => 'Barangay',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => 'Postal Code',
            'home_phone' => 'Home Phone',
            'mobile_phone' => 'Mobile Phone',
            'email_address_1' => 'Email Address 1',
            'email_address_2' => 'Email Address 2',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($row[$field])) {
                $failure = new Failure(
                    $excelRow,
                    $field,
                    ["Contact Info: {$label} is required"]
                );
                $this->failures[] = $failure;
            }
        }

        return true;
    }

    public function collection(Collection $collection)
    {
        $processedEmpIds = []; // Track which emp_ids were processed
        
        // Process rows in the collection
        foreach ($collection as $index => $row) {
            // Increment row counter for each row in collection
            $this->currentRow++;
            $excelRow = $this->currentRow;

            // Check if PersonalInfo exists for this row
            // If employee_id is empty in PersonalInfo, skip this row
            if (!isset($this->rows[$excelRow]) || empty($this->rows[$excelRow]['emp_id'])) {
                continue; 
            }

            // Get role from stored data
            $role = $this->rows[$excelRow]['role'] ?? null;

            // Check if row is completely empty
            $isEmptyRow = true;
            foreach ($row as $value) {
                if ($value !== null && $value !== '') {
                    $isEmptyRow = false;
                    break;
                }
            }

            // Handle validation and creation based on role and data presence
            if ($isEmptyRow) {
                // Row is completely empty
                if (!in_array($role, $this->rolesWithLimitedInfo)) {
                    // Empty contact info is not allowed for roles that are not in rolesWithLimitedInfo
                    continue;
                } else {
                    // Contact info is allowed for roles thatare in rolesWithLimitedInfo
                    // Continue to create Employee record below with NULL values
                }
            } else {
                // Row has some data - validate if roles not in rolesWithLimitedInfo
                if (!in_array($role, $this->rolesWithLimitedInfo)) {
                    // Roles not in rolesWithLimitedInfo with data: validate all required fields
                    $this->validateRequiredFields($row, $excelRow);
                }
            }

            // Track that we're processing this emp_id
            $processedEmpIds[] = $this->rows[$excelRow]['emp_id'];

            // If validateCells don't save to database
            if ($this->validateCells) {
                continue;
            }

            // Create Employee record with contact info or null if empty for roles in rolesWithLimitedInfo
            $data = array_merge(
                $this->rows[$excelRow],
                [
                    'emp_houseno' => !empty($row['house_number']) ? $row['house_number'] : null,
                    'street'   => !empty($row['street']) ? $row['street'] : null,
                    'brgy' => !empty($row['barangay']) ? $row['barangay'] : null,
                    'city'   => !empty($row['city']) ? $row['city'] : null,
                    'province' => !empty($row['province']) ? $row['province'] : null,
                    'postal_code'   => !empty($row['postal_code']) ? $row['postal_code'] : null,
                    'info_status' => 'Active',
                    'home_phone' => !empty($row['home_phone']) ? $row['home_phone'] : null,
                    'mobile_phone'   => !empty($row['mobile_phone']) ? $row['mobile_phone'] : null,
                    'email_address_1' => !empty($row['email_address_1']) ? $row['email_address_1'] : null,
                    'email_address_2' => !empty($row['email_address_2']) ? $row['email_address_2'] : null,
                ]
            );

            Employee::create($data);
        }

        // Handles rows that were skipped by Laravel Excel
        foreach ($this->rows as $excelRow => $rowData) {
            // Skip if no emp_id
            if (empty($rowData['emp_id'])) {
                continue;
            }

            // Check if this emp_id was already processed 
            if (in_array($rowData['emp_id'], $processedEmpIds)) {
                continue; // Already handled
            }

            // Get role for this row
            $role = $rowData['role'] ?? null;

            if (!in_array($role, $this->rolesWithLimitedInfo)) {
                //If not in rolesWithLimitedInfo Contact info is required
                $failure = new Failure(
                    $excelRow,
                    'contact_info',
                    ["Contact Info: All contact fields are empty."]
                );
                $this->failures[] = $failure;
                continue; // Don't create Employee record
            }

            if (!$this->validateCells) {
                $data = array_merge(
                    $rowData,
                    [
                        'emp_houseno' => null,
                        'street' => null,
                        'brgy' => null,
                        'city' => null,
                        'province' => null,
                        'postal_code' => null,
                        'info_status' => 'Active',
                        'home_phone' => null,
                        'mobile_phone' => null,
                        'email_address_1' => null,
                        'email_address_2' => null,
                    ]
                );

                Employee::create($data);
            }
        }
    }
}