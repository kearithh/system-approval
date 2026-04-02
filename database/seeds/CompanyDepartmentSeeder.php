<?php

use Illuminate\Database\Seeder;

class CompanyDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'short_name' => 'FND',
                'name_en' => 'Finance Department',
                'name_km' => 'នាយកដ្ឋានហិរញ្ញវត្ថុ',
                'description' => '',
            ],
            [
                'short_name' => 'ITD',
                'name_en' => 'IT Department',
                'name_km' => 'នាយកដ្ឋានព័ត៌មានវិទ្យា',
                'description' => '',
            ],
            [
                'short_name' => 'HAD',
                'name_en' => 'Human Resource and Admin',
                'name_km' => 'នាយកដ្ឋានធនធានមនុស្ស និងរដ្ឋបាល',
                'description' => '',
            ],
            [
                'short_name' => 'IAD',
                'name_en' => 'Internal Auditor Department',
                'name_km' => 'នាយកដ្ឋានសវនកម្មផ្ទៃក្នុង',
                'description' => '',
            ],
            [
                'short_name' => 'OPD',
                'name_en' => 'Operation Department',
                'name_km' => 'នាយកដ្ឋានប្រតិបត្ដិការ',
                'description' => '',
            ],
            [
                'short_name' => 'CPL',
                'name_en' => 'Compliance Department',
                'name_km' => 'នាយកដ្ឋានប្រតិបត្តិបតាម',
                'description' => '',
            ],
            [
                'short_name' => 'CBO',
                'name_en' => 'Business Management Department',
                'name_km' => 'នាយកដ្ឋានគ្រប់គ្រងអាជីវកម្ម',
                'description' => '',
            ],
        ];

        $companies = \App\Company::all();
        foreach ($companies as $value) {
            $value->updateRecord($value->id, ['department' => $departments]);
        }
    }
}
