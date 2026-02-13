<?php

namespace App\Imports\Sheets\Users;

use App\Models\Employee_Login;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class LoginInfoSheet implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithEvents
{
    protected $rows;
    protected $failures = [];
    protected $validateCells = false;
    protected $currentRow = 7;

    public function __construct(&$rows, $validateCells = false)
    {
        $this->rows = &$rows;
        $this->validateCells = $validateCells;
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
            'email' => [
                'required',
                'email',
                Rule::unique('tbl_login', 'email'),
            ],
            'role' => 'required|in:Employee,SuperAdmin,HR Admin,Dean,IDC Admin,ISO Document Handler',
            'password' => 'required'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.required' => 'Login Info: Email is required',
            'email.email' => 'Login Info: Email must be valid',
            'email.unique' => 'Login Info: Email must be unique',
            'role.required' => 'Login Info: Role is required',
            'role.in' => 'Login Info: Role must be Employee, SuperAdmin, HR Admin, Dean, IDC Admin, or ISO Document Handler',
            'password.required' => 'Login Info: Password is required',
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

            // Check if PersonalInfo exists for this row
            // If employee_id is empty in PersonalInfo, skip this row
            if (!isset($this->rows[$excelRow]) || empty($this->rows[$excelRow]['emp_id'])) {
                continue; 
            }

            // Store roles in rows array for other sheets
            $this->rows[$excelRow]['role'] = $row['role'];

            // If validateCells, don't save to database
            if ($this->validateCells) {
                continue;
            }

            $data = [
                'id' => $this->rows[$excelRow]['emp_id'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'role' => $row['role'],
            ];

            Employee_Login::create($data);
        }
    }
}