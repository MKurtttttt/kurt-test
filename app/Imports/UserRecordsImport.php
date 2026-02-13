<?php

namespace App\Imports;

use App\Imports\Sheets\Users\PersonalInfoSheet;
use App\Imports\Sheets\Users\ContactInfoSheet;
use App\Imports\Sheets\Users\ProvincialContactInfoSheet;
use App\Imports\Sheets\Users\EmergencyContactInfoSheet;
use App\Imports\Sheets\Users\AccountingDetailsSheet;
use App\Imports\Sheets\Users\HiringInfoSheet;
use App\Imports\Sheets\Users\LoginInfoSheet;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserRecordsImport implements WithMultipleSheets
{
    protected array $rows = [];
    protected $validateCells = false;
    protected $sheets = [];
    protected $rolesWithLimitedInfo = ['SuperAdmin', 'HR Admin', 'Dean', 'IDC Admin', 'ISO Document Handler'];

    public function __construct($validateCells = false)
    {
        $this->validateCells = $validateCells;
    }

    public function sheets(): array
    {
        $this->sheets = [
            'personal' => new PersonalInfoSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
            'login' => new LoginInfoSheet($this->rows, $this->validateCells),
            'contact' => new ContactInfoSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
            'provincial' => new ProvincialContactInfoSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
            'emergency' => new EmergencyContactInfoSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
            'accounting' => new AccountingDetailsSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
            'hiring' => new HiringInfoSheet($this->rows, $this->validateCells, $this->rolesWithLimitedInfo),
        ];

        return [
            'Personal_Info' => $this->sheets['personal'],      
            'Login_Info'    => $this->sheets['login'],        
            'Contact_Info'  => $this->sheets['contact'],       
            'Prov_Contact_Info'  => $this->sheets['provincial'],
            'Em_Contact_Info'  => $this->sheets['emergency'],
            'Acc_Details'  => $this->sheets['accounting'],
            'Hiring_Info'    => $this->sheets['hiring'],
        ];
    }

    /**
     * Get all validation failures from all sheets
     */
    public function failures()
    {
        $allFailures = [];

        // Validate PersonalInfo fields that depend on role
        if (method_exists($this->sheets['personal'], 'validateAfterRoleAssignment')) {
            $this->sheets['personal']->validateAfterRoleAssignment();
        }
        
        foreach ($this->sheets as $sheet) {
            if (method_exists($sheet, 'getFailures')) {
                $allFailures = array_merge($allFailures, $sheet->getFailures());
            }
        }
        
        return $allFailures;
    }
}