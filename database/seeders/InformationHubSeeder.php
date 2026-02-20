<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InformationHubLinks;

class InformationHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InformationHubLinks::create([
            'category' => 'ISO Manuals',
            'type' => 'Document',
            'image_path' => 'images/information_hub/iso_manuals/eoms_manual.png',
            'title' => 'EOMS Manual',
            'url' => 'https://hauph.sharepoint.com/:b:/s/ISO-OIE/IQCPyVkrESnsSpnXRHBfCRNMARAYW-sp0u5SEAm9MHtluZU?e=NMQWRZ'
        ]);

        InformationHubLinks::create([
            'category' => 'ISO Manuals',
            'type' => 'Document',
            'image_path' => 'images/information_hub/iso_manuals/records_management_manual.png',
            'title' => 'Records Management Manual',
            'url' => 'https://hauph.sharepoint.com/:b:/s/ISO-OIE/IQC1CseTRwqSRpGeCN2nbsAdAUYyZRMp1Ryb8q88pk5yFW8?e=ShWEQh'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep1.png',
            'title' => 'QualiThink_EOMS_Episode 01_Introduction to EOMS',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQBwX8kxMhLGToM7MzVCdbxBAWBbVrEFW9NO9nzWhrztLBs?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=1zec7o'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep2.png',
            'title' => 'QualiThink_EOMS_Episode 02_HAU EOMS Quality Policy',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQAWIQKbmfO3QbEWRmHug6VRAVqA5E0hxEGsTTK1rM7JR5M?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=i6BQTQ'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep3.png',
            'title' => 'QualiThink_EOMS_Episode 03_HAU MVV Statement',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQBO8VQN4RnzRKkTSfzA3CQhAY5aFGrFIi0udov5tVDoqco?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=PGbQo1'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep4.png',
            'title' => 'QualiThink_EOMS_Episode 04_Roles and Responsibilities',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQCx_LA26KDgRp-Q_uqxjl6YAdve5uwCFclD8zmWmy9Q_TM?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=29uvqz'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep5.png',
            'title' => 'QualiThink_EOMS_Episode 05_Risk-Based Thinking and Continuous Improvement',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQAxGySesPXBTaDrH1JdYWO_AaZGf2Be-DzTy3m1au4_GdA?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=mK7Njn'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep6.png',
            'title' => 'QualiThink_EOMS_Episode 06_Evidence-Based Decision-Making',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQC10ok10pI4TrkuQRM_2FpGAb8bI3VFr5gD6dVf37xoWp4?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=sxo6eN'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep7.png',
            'title' => 'QualiThink_EOMS_Episode 07_Engaging Stakeholders in EOMS Implementation',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQAArCOfduKtRY3H3SkrQG4PAY-CLzWv50xwKn8q-_5L_i0?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=wGE8BT'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep8.png',
            'title' => 'QualiThink_EOMS_Episode 08_EOMS Documents and Records You Should Know',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQA5tpQl2KcaTobCJzl62w2iAT0RNpk2zFqVRY__r1ONlHs?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=Gb38ks'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep9.png',
            'title' => 'QualiThink_EOMS_Episode 09_HAU\'s Strategic Themes and Objectives',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQDlVuTfe7xuRIwPm5dJN2DSAYJRpJP_ahP9pr29FYXFVW0?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=tz6sWn'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep10.png',
            'title' => 'QualiThink_EOMS_Episode 10_Internal Audits-Why They Matter and How You’re Involved',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQCsbly7W6PmSJAIsA0whzYwAUNtKoaeXaA854C8NTFrvSs?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=7M5lX1'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep11.png',
            'title' => 'QualiThink_EOMS_Episode 11_The Relevance and Role of Management Review',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQBL3AmoxrDOTZooRaDEYw-lAWCPBqVLBWUZkKDwn0WbqFg?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=qGsO1I'
        ]);

        InformationHubLinks::create([
            'category' => 'Learning Videos',
            'sub_category' => 'QualiThink: EOMS Awareness Videos',
            'type' => 'Video',
            'image_path' => 'images/information_hub/learnings/qualithink_eoms_ep12.png',
            'title' => 'QualiThink_EOMS_Episode 12_EOMS and Your Everyday Work-Making Quality a Habit',
            'url' => 'https://hauph-my.sharepoint.com/:v:/g/personal/oie-hau_hau_edu_ph/IQBGIAqYkEVLQY99uQAztasHAcBQoZmNWUDB2YeK4xVC67o?nav=eyJyZWZlcnJhbEluZm8iOnsicmVmZXJyYWxBcHAiOiJPbmVEcml2ZUZvckJ1c2luZXNzIiwicmVmZXJyYWxBcHBQbGF0Zm9ybSI6IldlYiIsInJlZmVycmFsTW9kZSI6InZpZXciLCJyZWZlcnJhbFZpZXciOiJNeUZpbGVzTGlua0NvcHkifX0&e=6tNBVF'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2019-20_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2019-20_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQDarcEcJAJ9SLOn3ocBZt8pAQ9GXIg62Fi8EcY9CSKfVl8?e=uRXqPZ'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2020-21_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2020-21_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQBqazIQNHdMQoHNVD_CnP2LATsQ2pC9qgLU5G4WdFcyqtY?e=dZbw5S'
        ]);

        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2021-22_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2021-22_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQD-GLRXL40URp5tFZMyzxhKAS0718b0HuCgzR7s9wQTa3c?e=csddEX'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2022-23_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2022-23_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQAwKQRYVnRIRJJarBX4fWsaAewxlsqNjwCHUSV4j4V_8mY?e=tqv9uW'
        ]);
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2023-24_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2023-24_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQBMghgVKrYjR4gzjE-TmnuJASZYgGBsuAChpE1TFgOTqZI?e=trFa1Y'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2024-25_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2024-25_College',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQDa52oqEspHTKuwTqYYBBWaAUvpnVMhAfRGnM1HVi27J7s?e=SMGNnJ'
        ]);
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2025-26_ch.jpg',
            'title' => 'Student Satisfaction Survey Report 2025-26_College and High School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQBIHqfyIoaKToCMxaqXLIomATvTxbmTxtvE4CBvZP_F3lc?e=0JxJMR'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2022-23_gs.jpg',
            'title' => 'Student Satisfaction Survey Report 2022-23_Graduate School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQDI1PDL8o2MSL7UVsods2nbAQrAMcFQSpwwVLMKsFSonnM?e=nuJZ6G'
        ]);
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2023-24_gs.jpg',
            'title' => 'Student Satisfaction Survey Report 2023-24_Graduate School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQDHX3i0s91US43vKw5eij-QASek_4hJwCYrgIFYQkd9eQU?e=YcDQ9f'
        ]);
        
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2024-25_gs.jpg',
            'title' => 'Student Satisfaction Survey Report 2024-25_Graduate School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQCKluemPd1lTJGCb_Qn-8qAAYfLiihiTvo0-Lxs3-zr6rc?e=G1B3tz'
        ]);
        InformationHubLinks::create([
            'category' => 'Reports',
            'sub_category' => 'Student Satisfaction',
            'type' => 'Document',
            'image_path' => 'images/information_hub/reports/sssr_2025-26_gs.jpg',
            'title' => 'Student Satisfaction Survey Report 2025-26_Graduate School',
            'url' => 'https://hauph.sharepoint.com/:b:/s/IPRPO207/IQBKuCtAPQ4rQY7VLjahcmvTAV6EB5UKQ-Vm_O3NB7ceo_I?e=hjg73H'
        ]);
        
    }
}
