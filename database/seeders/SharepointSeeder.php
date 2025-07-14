<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SharepointDepartment;
use App\Models\Office;
use App\Models\SharepointLinks;

class SharepointSeeder extends Seeder
{
    public function run(): void
    {
        // DOC_LOG link
        SharepointLinks::create([
            'label' => 'DOC_LOG',
            'url' => 'https://hauph.sharepoint.com/sites/DOC_LOG/Shared%20Documents/Forms/AllItems.aspx',
            'category' => 'ISO',
            'department' => 'DOC_LOG',
            'office' => null,
        ]);

        // HAU-ISO 21001:2018 link
        SharepointLinks::create([
            'label' => 'HAU-ISO 21001:2018',
            'url' => 'https://hauph.sharepoint.com/sites/HAU-ISO210012018/Shared%20Documents/Forms/AllItems.aspx',
            'category' => 'ISO',
            'department' => 'HAU-ISO 21001:2018',
            'office' => null,
        ]);

        // OFFICE OF THE PRESIDENT (OOP) links
        $OOPLinks = [
            [ //OOP: OFFICE OF THE PRESIDENT
                'office' => 'OOP: OFFICE OF THE PRESIDENT', 
                'label' => 'ISO-OOP', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OOP/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-CKS: CENTER FOR KAPAMPANGAN STUDIES
                'office' => 'OOP-CKS: CENTER FOR KAPAMPANGAN STUDIES', 
                'label' => 'ISO-OOP-CKS', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CKS/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-ITS: INFORMATION TECHNOLOGY SYSTEMS SERVICES
                'office' => 'OOP-ITS: INFORMATION TECHNOLOGY SYSTEMS SERVICES', 
                'label' => 'ISO-OOP-ITS', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-ITS/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-OIA: OFFICE OF INTERNATIONAL AFFAIRS
                'office' => 'OOP-OIA: OFFICE OF INTERNATIONAL AFFAIRS', 
                'label' => 'ISO-OOP-OIA', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OIA/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-ITC: INSTITUTIONAL TESTING CENTER
                'office' => 'OOP-ITC: INSTITUTIONAL TESTING CENTER', 
                'label' => 'ISO-OOP-ITC', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-ITC/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-DPO: DATA PRIVACY OFFICE
                'office' => 'OOP-DPO: DATA PRIVACY OFFICE', 
                'label' => 'ISO-OOP-DPO', 
                'url' => 'https://hauph.sharepoint.com/sites/iso-oop-dpo/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-TRO: TREASURY OFFICE
                'office' => 'OOP-TRO: TREASURY OFFICE', 
                'label' => 'ISO-OOP-TRO', 
                'url' => 'https://hauph.sharepoint.com/sites/iso-oop-tro/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-UCO: UNIVERSITY CHAPLAIN OFFICE
                'office' => 'OOP-UCO: UNIVERSITY CHAPLAIN OFFICE', 
                'label' => 'ISO-OOP-UCO', 
                'url' => 'https://hauph.sharepoint.com/sites/iso-oop-uco/Shared%20Documents/Forms/AllItems.aspx'
                ],
            [ //OOP-AVI: AVIATION INSTITUTE
                'office' => 'OOP-AVI: AVIATION INSTITUTE', 
                'label' => 'ISO-OOP-AVI', 
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OOP-AVI/Shared%20Documents/Forms/AllItems.aspx'
                ],
        ];

        foreach ($OOPLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'OFFICE OF THE PRESIDENT (OOP)',
                'office' => $item['office'],
            ]);
        }


        // ACADEMIC AFFAIRS CLUSTER (AAC) links
        $AACLinks = [
            [ //AAC: ACADEMIC AFFAIRS OFFICE
                'office' => 'AAC: ACADEMIC AFFAIRS OFFICE',
                'label' => 'ISO-AAC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-LMS: LEARNING MANAGEMENT SYSTEM
                'office' => 'AAC-LMS: LEARNING MANAGEMENT SYSTEM',
                'label' => 'ISO-AAC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-CTL: CENTER FOR TEACHING & LEARNING
                'office' => 'AAC-CTL: CENTER FOR TEACHING & LEARNING',
                'label' => 'ISO-AAC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-IRB: INSTITUTIONAL REVIEW BOARD
                'office' => 'AAC-IRB: INSTITUTIONAL REVIEW BOARD',
                'label' => 'ISO-AAC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-GSR: GRADUATE STUDIES & RESEARCH
                'office' => 'AAC-GSR: GRADUATE STUDIES & RESEARCH',
                'label' => 'ISO-AAC-GSR',
                'url' => 'https://hauph.sharepoint.com/sites/iso-aac-gsr/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SAS: SCHOOL OF ARTS & SCIENCES
                'office' => 'AAC-SAS: SCHOOL OF ARTS & SCIENCES',
                'label' => 'ISO-AAC-SAS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SAS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SBA: SCHOOL OF BUSINESS & ACCOUNTANCY
                'office' => 'AAC-SBA: SCHOOL OF BUSINESS & ACCOUNTANCY',
                'label' => 'ISO-AAC-SBA',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SBA/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SED: SCHOOL OF EDUCATION
                'office' => 'AAC-SED: SCHOOL OF EDUCATION',
                'label' => 'ISO-AAC-SED',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SED/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-CJE: COLLEGE OF CRIMINAL JUSTICE EDUCATION & FORENSICS
                'office' => 'AAC-CJE: COLLEGE OF CRIMINAL JUSTICE EDUCATION & FORENSICS',
                'label' => 'ISO-AAC-CJE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CJE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SEA: SCHOOL OF ENGINEERING & ARCHITECTURE
                'office' => 'AAC-SEA: SCHOOL OF ENGINEERING & ARCHITECTURE',
                'label' => 'ISO-AAC-SEA',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SEA/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-STM: SCHOOL OF HOSPITALITY & TOURISM MANAGEMENT
                'office' => 'AAC-STM: SCHOOL OF HOSPITALITY & TOURISM MANAGEMENT',
                'label' => 'ISO-AAC-STM',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SHTM/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SOC: SCHOOL OF COMPUTING
                'office' => 'AAC-SOC: SCHOOL OF COMPUTING',
                'label' => 'ISO-AAC-SOC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SOC/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-SNA: SCHOOL OF NURSING & ALLIED MEDICAL SCIENCES
                'office' => 'AAC-SNA: SCHOOL OF NURSING & ALLIED MEDICAL SCIENCES',
                'label' => 'ISO-AAC-SNA',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-NAM/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-BED: BASIC EDUCATION
                'office' => 'AAC-BED: BASIC EDUCATION',
                'label' => 'ISO-AAC-BED',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-BED/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-LIB: LIBRARY DEPARTMENT
                'office' => 'AAC-LIB: LIBRARY DEPARTMENT',
                'label' => 'ISO-AAC-LIB',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-LIB/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AAC-URO: UNIVERSITY RESEARCH OFFICE
                'office' => 'AAC-URO: UNIVERSITY RESEARCH OFFICE',
                'label' => 'ISO-AAC-URO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-URO/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($AACLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'ACADEMIC AFFAIRS CLUSTER (AAC)',
                'office' => $item['office'],
            ]);
        }


        // INSTITUTE FOR CHRISTIAN FORMATION & SOCIAL INTEGRATION (CFS) links
        $CFSLinks = [
            [ //CFS-CES: OFFICE OF COMMUNITY EXTENSION SERVICES
                'office' => 'CFS-CES: OFFICE OF COMMUNITY EXTENSION SERVICES',
                'label' => 'ISO-CFS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CFS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CFS-CMO: CAMPUS MINISTRY OFFICE
                'office' => 'CFS-CMO: CAMPUS MINISTRY OFFICE',
                'label' => 'ISO-CFS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CFS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CFS-CLE: CHRISTIAN LIVING EDUCATION
                'office' => 'CFS-CLE: CHRISTIAN LIVING EDUCATION',
                'label' => 'ISO-CFS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CFS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CFS-CEP: CHARACTER EDUCATION PROGRAM DESK
                'office' => 'CFS-CEP: CHARACTER EDUCATION PROGRAM DESK',
                'label' => 'ISO-CFS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CFS/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($CFSLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'INSTITUTE FOR CHRISTIAN FORMATION & SOCIAL INTEGRATION (CFS)',
                'office' => $item['office'],
            ]);
        }


        // OFFICE OF INSTITUTIONAL EFFECTIVENESS (OIE) links
        $OIELinks = [
            [ //OIE-QAO: QUALITY ASSURANCE OFFICE
                'office' => 'OIE-QAO: QUALITY ASSURANCE OFFICE',
                'label' => 'ISO-OIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //OIE-IPR: INSTITUTIONAL RESEARCH, PLANNING & PUBLICATIONS OFFICE
                'office' => 'OIE-IPR: INSTITUTIONAL RESEARCH, PLANNING & PUBLICATIONS OFFICE',
                'label' => 'ISO-OIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //OIE-DMO: INSTITUTIONAL DATABASE MANAGEMENT OFFICE
                'office' => 'OIE-DMO: INSTITUTIONAL DATABASE MANAGEMENT OFFICE',
                'label' => 'ISO-OIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //OIE-IDC: INSTITUTIONAL DOCUMENT CONTROLLER
                'office' => 'OIE-IDC: INSTITUTIONAL DOCUMENT CONTROLLER',
                'label' => 'ISO-OIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-OIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($OIELinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'OFFICE OF INSTITUTIONAL EFFECTIVENESS (OIE)',
                'office' => $item['office'],
            ]);
        }


        // HUMAN RESOURCE MANAGEMENT OFFICE (HRO)
        $HROLinks = [
            [ //HRO-HRM: RECRUITMENT & MAINTENANCE
                'office' => 'HRO-HRM: RECRUITMENT & MAINTENANCE',
                'label' => 'ISO-HRO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-HRMO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //HRO-HRD: HUMAN RESOURCE DEVELOPMENT
                'office' => 'HRO-HRD: HUMAN RESOURCE DEVELOPMENT',
                'label' => 'ISO-HRO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-HRMO/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($HROLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'HUMAN RESOURCE MANAGEMENT OFFICE (HRO)',
                'office' => $item['office'],
            ]);
        }


        // FINANCE & RESOURCES MANAGEMENT SERVICES (FRM)
        $FRMLinks = [
            [ //FRM: FINANCE & RESOURCES MANAGEMENT OFFICE
                'office' => 'FRM: FINANCE & RESOURCES MANAGEMENT OFFICE',
                'label' => 'ISO-FRM',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FIN/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-GRT: GRANTS ACCOUNTANT
                'office' => 'FRM-GRT: GRANTS ACCOUNTANT',
                'label' => 'ISO-FRM-GRT',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FIN/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-ATO: ACCOUNTING OFFICE
                'office' => 'FRM-ATO: ACCOUNTING OFFICE',
                'label' => 'ISO-FRM-ATO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-PAO: PAYROLL OFFICE
                'office' => 'FRM-PAO: PAYROLL OFFICE',
                'label' => 'ISO-FRM-PAO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FPO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-ACC: ACCOUNTS & COLLECTION
                'office' => 'FRM-ACC: ACCOUNTS & COLLECTION',
                'label' => 'ISO-FRM-ACC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FAC/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-ASE: ANCILLARY SERVICES
                'office' => 'FRM-ASE: ANCILLARY SERVICES',
                'label' => 'ISO-FRM-ASE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FAS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //FRM-ASA: ANCILLARY SERVICES - ACCOUNTING
                'office' => 'FRM-ASA: ANCILLARY SERVICES - ACCOUNTING',
                'label' => 'ISO-FRM-ASE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-FAS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            
        ];

        foreach ($FRMLinks as $item) {
             SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'FINANCE & RESOURCES MANAGEMENT SERVICES (FRM)',
                'office' => $item['office'],
            ]);
        }


        // RECORDS SYSTEMS & SERVICES (RSS)
        $RSSLinks = [
            [ //RSS: RECORDS SYSTEMS & SERVICES
                'office' => 'RSS: RECORDS SYSTEMS & SERVICES',
                'label' => 'ISO-RSS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-RSS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //RSS-ADO: ADMISSIONS OFFICE
                'office' => 'RSS-ADO: ADMISSIONS OFFICE',
                'label' => 'ISO-RSS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-RSS/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($RSSLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'RECORDS SYSTEMS & SERVICES (RSS)',
                'office' => $item['office'],
            ]);
        }


        // STUDENT SERVICES & AFFAIRS (SSA)
        $SSALinks = [
            [ //SSA-SAO: STUDENT AFFAIRS OFFICE
                'office' => 'SSA-SAO: STUDENT AFFAIRS OFFICE',
                'label' => 'ISO-SSA-SAO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //SSA-USO: UNIVERSITY SPORTS OFFICE
                'office' => 'SSA-USO: UNIVERSITY SPORTS OFFICE',
                'label' => 'ISO-SSA-USO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SAO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //SSA-UGC: UNIVERSITY GUIDANCE CENTER
                'office' => 'SSA-UGC: UNIVERSITY GUIDANCE CENTER',
                'label' => 'ISO-SSA-UGC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-UGO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //SSA-SGO: UNIVERSITY SCHOLARCHIPS & GRANTS OFFICE
                'office' => 'SSA-SGO: UNIVERSITY SCHOLARCHIPS & GRANTS OFFICE',
                'label' => 'ISO-SSA-SGO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-SGO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //SSA-CPO: CARREER & PLACEMENT OFFICE
                'office' => 'SSA-CPO: CARREER & PLACEMENT OFFICE',
                'label' => 'ISO-SSA-CPO',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CPO/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //SSA-MDS: MEDICAL & DENTAL CLINIC
                'office' => 'SSA-MDS: MEDICAL & DENTAL CLINIC',
                'label' => 'ISO-SSA-MDS',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-MEDDEN/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($SSALinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'STUDENT SERVICES & AFFAIRS (SSA)',
                'office' => $item['office'],
            ]);
        }


        // EXTERNAL AFFAIRS CLUSTER (EAC)
        $EACLinks = [
            [ //EAC: EXTERNAL AFFAIRS CLUSTER
                'office' => 'EAC: EXTERNAL AFFAIRS CLUSTER',
                'label' => 'ISO-EAC',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-MCS/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //EAC-PAM: PERFORMING ARTS & EVENTS MANAGEMENT
                'office' => 'EAC-PAM: PERFORMING ARTS & EVENTS MANAGEMENT',
                'label' => 'ISO-EAC-PAM',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-PAM/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //EAC-ARO: ALUMNI RELATIONS OFFICE
                'office' => 'EAC-ARO: ALUMNI RELATIONS OFFICE',
                'label' => 'ISO-EAC-ARO',
                'url' => 'https://hauph.sharepoint.com/sites/iso-eac-aro/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //EAC-CRE: CREATIVES SERVICES
                'office' => 'EAC-CRE: CREATIVES SERVICES',
                'label' => 'ISO-EAC-CRE',
                'url' => 'https://hauph.sharepoint.com/sites/iso-eac-pro/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //EAC-PRO: PUBLIC RELATIONS OFFICE
                'office' => 'EAC-PRO: PUBLIC RELATIONS OFFICE',
                'label' => 'ISO-EAC-PRO',
                'url' => 'https://hauph.sharepoint.com/sites/iso-eac-pro/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($EACLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'EXTERNAL AFFAIRS CLUSTER (EAC)',
                'office' => $item['office'],
            ]);
        }


        // CAMPUS SERVICES & DEVELOPMENT OFFICE (CSD)
        $CSDLinks = [
            [ //CSD-PUO: PURCHASING OFFICE
                'office' => 'CSD-PUO: PURCHASING OFFICE',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-CSO: CAMPUS SERVICES OFFICE
                'office' => 'CSD-CSO: CAMPUS SERVICES OFFICE',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-PCO: PROPERTY CUSTODIAN SHIP
                'office' => 'CSD-PCO: PROPERTY CUSTODIAN SHIP',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-VLO: VENUES & LOGISTICS OFFICE
                'office' => 'CSD-VLO: VENUES & LOGISTICS OFFICE',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-MCM: MOTORPOOL/CAMPUS MAINTENANACE
                'office' => 'CSD-MCM: MOTORPOOL/CAMPUS MAINTENANACE',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-SEC: CAMPUS SECURITY
                'office' => 'CSD-SEC: CAMPUS SECURITY',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //CSD-ECM: ENGINEERING CONSTRUCTION & MAINTENANCE
                'office' => 'CSD-ECM: ENGINEERING CONSTRUCTION & MAINTENANCE',
                'label' => 'ISO-CSD',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-CSD/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($CSDLinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'CAMPUS SERVICES & DEVELOPMENT OFFICE (CSD)',
                'office' => $item['office'],
            ]);
        }


        // INSTITUTE FOR ACADEMIC INNOVATION & ENTREPRENEURSHIP (AIE)
        $AIELinks = [
            [ //AIE-SPL: SCHOOL OF PROFESSIONAL EDUCATION & LIFELONG LEARNING
                'office' => 'AIE-SPL: SCHOOL OF PROFESSIONAL EDUCATION & LIFELONG LEARNING',
                'label' => 'ISO-AIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AIE-ETA: EXPANDED TERTIARY EDUCATION, EQUIVALENCY & ACCREDITATION
                'office' => 'AIE-ETA: EXPANDED TERTIARY EDUCATION, EQUIVALENCY & ACCREDITATION',
                'label' => 'ISO-AIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
            [ //AIE-TBI: TECHNOLOGY BUSINESS INCUBATOR - KITTO
                'office' => 'AIE-TBI: TECHNOLOGY BUSINESS INCUBATOR - KITTO',
                'label' => 'ISO-AIE',
                'url' => 'https://hauph.sharepoint.com/sites/ISO-AIE/Shared%20Documents/Forms/AllItems.aspx'
            ],
        ];

        foreach ($AIELinks as $item) {
            SharepointLinks::create([
                'label' => $item['label'],
                'url' => $item['url'],
                'category' => 'ISO',
                'department' => 'INSTITUTE FOR ACADEMIC INNOVATION & ENTREPRENEURSHIP (AIE)',
                'office' => $item['office'],
            ]);
        }


        // INTERNAL AUDIT TEAM (IAT)
        SharepointLinks::create([
            'label' => 'ISO-IAT 21001',
            'url' => 'https://hauph.sharepoint.com/sites/ISO-IQA21001/Shared%20Documents/Forms/AllItems.aspx',
            'category' => 'ISO',
            'department' => 'INTERNAL AUDIT TEAM (IAT)',
            'office' => 'INTERNAL QUALITY AUDIT',
        ]);


        // MID-YEAR REVIEW
        SharepointLinks::create([
            'label' => 'MID-YEAR REVIEW',
            'url' => 'https://hauph.sharepoint.com/sites/Mid-YearReview',
            'category' => 'Planning and Review',
            'department' => 'INSTITUTIONAL PLANNING, RESEARCH & PUBLICATIONS OFFICE',
        ]);


        // YEAR-END REVIEW
        SharepointLinks::create([
            'label' => 'YEAR-END REVIEW',
            'url' => 'https://hauph.sharepoint.com/sites/Year-endReview',
            'category' => 'Planning and Review',
            'department' => 'INSTITUTIONAL PLANNING, RESEARCH & PUBLICATIONS OFFICE',
        ]);


        // OPERATIONAL PLANS
        SharepointLinks::create([
            'label' => 'OPERATIONAL PLANS',
            'url' => 'https://hauph.sharepoint.com/sites/OperationalPlans',
            'category' => 'Planning and Review',
            'department' => 'INSTITUTIONAL PLANNING, RESEARCH & PUBLICATIONS OFFICE',
        ]);


        // STRATEGIC PLANS
        SharepointLinks::create([
            'label' => 'STRATEGIC PLANS',
            'url' => 'https://hauph.sharepoint.com/sites/StrategicPlan2018-23',
            'category' => 'Planning and Review',
            'department' => 'INSTITUTIONAL PLANNING, RESEARCH & PUBLICATIONS OFFICE',
        ]);


        // HAU KPIs AND MEASURES
        SharepointLinks::create([
            'label' => 'HAU KPIs AND MEASURES',
            'url' => 'https://hauph.sharepoint.com/sites/PLANNING#',
            'category' => 'Planning and Review',
            'department' => 'INSTITUTIONAL PLANNING, RESEARCH & PUBLICATIONS OFFICE',
        ]);


        // OIE'S INSTITUTIONAL RECORDS HUB
        SharepointLinks::create([
            'label' => 'OIE\'S INSTITUTIONAL RECORDS HUB',
            'url' => 'https://hauph.sharepoint.com/sites/PLANNING#',
            'category' => 'Quality Assurance',
            'department' => 'QUALITY ASSURANCE',
        ]);


        // ACCREDITATION
        SharepointLinks::create([
            'label' => 'ACCREDITATION',
            'url' => 'https://hauph.sharepoint.com/sites/Accreditation',
            'category' => 'Quality Assurance',
            'department' => 'QUALITY ASSURANCE',
        ]);
    }
}