<?php

namespace Database\Seeders;

use App\Models\Departments;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // depts dummy data 
        Departments::create([ 
            'code' => 'HAU',
            'dept' => 'Holy Angel University', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'OOP',
            'dept' => 'Office of the President', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'OIE',
            'dept' => 'Office of Institutional Effectiveness', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CKS',
            'dept' => 'Center for Kapampangan Studies', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ICFSI',
            'dept' => 'Institute for Christian Formation & Social Integration', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'OIA',
            'dept' => 'Office of International Affairs', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'HRO',
            'dept' => 'Human Resource Management Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FIN',
            'dept' => 'Finance & Resources Management Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'RSS',
            'dept' => 'Records Systems and Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'AAC',
            'dept' => 'Academic Affairs Cluster', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SSA',
            'dept' => 'Student Services & Affairs', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'MCS',
            'dept' => 'Marketing (External Affairs & Corporate Communications)', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ITS',
            'dept' => 'Information Technology Systems & Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CSD',
            'dept' => 'Campus Services & Development Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'AIE',
            'dept' => 'Institute for Academic Innovation & Entrepreneurship', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'QAO',
            'dept' => 'Quality Assurance Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'IPR',
            'dept' => 'Institutional Research, Planning & Publications Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'DMO',
            'dept' => 'Institutional Database Management Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'HRM',
            'dept' => 'Recruitment and Maintenance', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'HRD',
            'dept' => 'Staff Development', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FAO',
            'dept' => 'Accounting', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FPO',
            'dept' => 'Payroll', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FAC',
            'dept' => 'Accounts & Collection', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FTO',
            'dept' => 'Treasury Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'FAS',
            'dept' => 'Ancillary Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ADO',
            'dept' => 'Admissions Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'GSR',
            'dept' => 'Graduate Studies & Research', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SAS',
            'dept' => 'School of Arts & Sciences', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SBA',
            'dept' => 'School of Business & Accountancy', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SED',
            'dept' => 'School of Education', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CJE',
            'dept' => 'College of Criminal Justice Education & Forensics', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SEA',
            'dept' => 'School of Engineering & Architecture', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'HTM',
            'dept' => 'School of Hospitality & Tourism Management', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SOC',
            'dept' => 'School of Computing', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'NAM',
            'dept' => 'School of Nursing & Allied Medical Sciences', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'BED',
            'dept' => 'Basic Education', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'LMS',
            'dept' => 'Learning Management System', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CTL',
            'dept' => 'Center for Teaching & Learning', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'IRB',
            'dept' => 'Institutional Review Board', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'LIB',
            'dept' => 'Library Department', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'URO',
            'dept' => 'University Research Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CES',
            'dept' => 'Office of the Community Extension Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CMO',
            'dept' => 'Campus Ministry Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CLE',
            'dept' => 'Christian Living Education', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SAO',
            'dept' => 'Student Affairs', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'USO',
            'dept' => 'University Sports', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'UGC',
            'dept' => 'University Guidance Center', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SGO',
            'dept' => 'Scholarships & Grants', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ITC',
            'dept' => 'Institutional Testing & Evaluation Center', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CPO',
            'dept' => 'Career and Placement Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'MED',
            'dept' => 'Medical Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'DEN',
            'dept' => 'Dental Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'PAM',
            'dept' => 'Performing Arts and Events Management', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ARE',
            'dept' => 'Alumni Relations', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'CRE',
            'dept' => 'Creative Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'PRO',
            'dept' => 'Public Relations Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'PUR',
            'dept' => 'Purchasing Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SER',
            'dept' => 'Campus Services Office', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'PCO',
            'dept' => 'Property Custodianship', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'VLO',
            'dept' => 'Venues and Logistics', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'MOT',
            'dept' => 'Motorpool/Campus Maintenance', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'TEL',
            'dept' => 'Telecommunications Services', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SEC',
            'dept' => 'Campus Security', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'DEV',
            'dept' => 'Campus Development', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ECM',
            'dept' => 'Engineering Construction and Maintenance', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'EMW',
            'dept' => 'Electrical & Mechanical Works', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'SPL',
            'dept' => 'School of Professional Education and Lifelong Learning', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'ETA',
            'dept' => 'Expanded Tertiary Education, Education Equivalency & Accreditation', 
            'logo' => ''
        ]);

        Departments::create([ 
            'code' => 'TBI',
            'dept' => 'Technology Business Incubator - KITTO', 
            'logo' => ''
        ]);
            
    }

}