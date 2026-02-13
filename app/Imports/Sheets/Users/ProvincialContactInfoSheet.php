<?php

namespace App\Imports\Sheets\Users;

use App\Models\provincial_contact;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class ProvincialContactInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
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
            'phone' => 'nullable|max:255',
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
        // Provincial Contact Info is not required for any role
        // All fields are optional
        return true;
    }

    public function collection(Collection $collection)
    {
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

            if ($isEmptyRow) {
                continue;
            }

            // Check if Personal Info exists
            if (!isset($this->rows[$excelRow]) || empty($this->rows[$excelRow]['emp_id'])) {
                continue; 
            }

            // Provincial contact is optional for all roles
            // If validateCells, don't save
            if ($this->validateCells) {
                continue;
            }

            $data = [
                'id' => $this->rows[$excelRow]['emp_id'],
                'pc_emp_houseno' => $row['house_number'],
                'pc_street' => $row['street'],
                'pc_brgy' => $row['barangay'],
                'pc_city' => $row['city'],
                'pc_province' => $row['province'],
                'pc_postal_code' => $row['postal_code'],
                'pc_phone' => $row['phone']
            ];

            provincial_contact::create($data);
        }
    }
}