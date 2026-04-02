<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //CEO
        DB::table('positions')->insert([
            'short_name' => 'CEO',
            'name_km' => 'ប្រធានក្រុមប្រឹក្សាភិបាល',
            'level' => config('app.position_level_ceo'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //ACEO
        DB::table('positions')->insert([
            'short_name' => 'ACEO',
            'name_km' => 'ជំនួយការប្រធាននាយកប្រតិបត្តិ',
            'level' => config('app.position_level_assistant_ceo'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //DCEO
        DB::table('positions')->insert([
            'short_name' => 'DCEO',
            'name_km' => 'អនុប្រធានប្រធាននាយកប្រតិបត្តិ',
            'level' => config('app.position_level_deputy_ceo'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //C
        $c = [
            [
                'short_name' => 'CFO',
                'name_km' => 'នាយកប្រតិបត្ដិហិរញ្ញវត្ថុ',
            ],
            [
                'short_name' => 'CBO',
                'name_km' => 'ប្រធាននាយកគ្រប់គ្រងអាជីវកម្ម',
            ],
            [
                'short_name' => 'COO',
                'name_km' => 'អគ្គនាយកប្រតិបត្ដិ',
            ],
            [
                'short_name' => 'CMO',
                'name_km' => 'នាយកប្រតិបត្ដិផ្នែកទីផ្សារ',
            ],
            [
                'short_name' => 'CIO',
                'name_km' => 'ប្រធាននាយព័ត៌មានវិទ្យា',
            ],

        ];
        foreach ($c as $item) {
            DB::table('positions')->insert([
                'short_name' => $item['short_name'],
                'name_km' => $item['name_km'],
                'level' => config('app.position_level_chef'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }


        //Head
        $data = collect([
            [
                'short_name' => 'HBD',
                'name_km' => 'ប្រធាននាយកដ្ឋានអភិវឌ្ឍន៌ធុរកិច្ច',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HBO',
                'name_km' => 'ប្រធាននាយកដ្ឋានប្រតិបត្តិកាធុរកិច្ច',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HFN',
                'name_km' => 'ប្រធាននាយកដ្ឋានហិរញ្ញវត្ថុ',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HHA',
                'name_km' => 'ប្រធាននាយកដ្ឋានធនធានមនុស្ស និងរដ្ឋបាល',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],[
                'short_name' => 'HIA',
                'name_km' => 'ប្រធាននាយកដ្ឋានសវនកម្មផ្ទៃក្នុង',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HIT',
                'name_km' => 'ប្រធាននាយកដ្ឋានព័ត៌មានវិទ្យា',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HHR',
                'name_km' => 'ប្រធាននាយកដ្ឋានធនធានមនុស្ស',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HOO',
                'name_km' => 'ប្រធាននាយកដ្ឋានប្រតិបត្តិការ',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HOC',
                'name_km' => 'ប្រធាននាយកដ្ឋានឥណទាន',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HAD',
                'name_km' => 'ប្រធាននាយកដ្ឋានរដ្ឋបាល',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],
            [
                'short_name' => 'HOM',
                'name_km' => 'ប្រធាននាយកដ្ឋានទីផ្សារ',
                'name_en' => '',
                'desc_kh' => '',
                'level' => '',
            ],

        ]);
        foreach ($data as $item) {
            DB::table('positions')->insert([
                'short_name' => $item['short_name'],
                'name_km' => $item['name_km'],
                'level' => config('app.position_level_head'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Deputy
        $deputy = [
            [
                'short_name' => 'DHBO',
                'name_km' => 'អនុប្រធាននាយកដ្ឋានប្រតិបត្តិការ',
            ],
            [
                'short_name' => 'DHFN',
                'name_km' => 'អនុប្រធាននាយកដ្ឋានហិរញ្ញវត្ថុ',
            ],
            [
                'short_name' => 'DHHA',
                'name_km' => 'អនុប្រធាននាយកដ្ឋានធនធានមនុស្ស និងរដ្ឋបាល',
            ],
            [
                'short_name' => 'DHIA',
                'name_km' => 'អនុប្រធាននាយកដ្ឋានសវនកម្មផ្ទៃក្នុង',
            ],
            [
                'short_name' => 'DHIT',
                'name_km' => 'អនុប្រធាននាយកដ្ឋានព័ត៌មានវិទ្យា',
            ]
        ];
        foreach ($deputy as $item) {
            DB::table('positions')->insert([
                'short_name' => $item['short_name'],
                'name_km' => $item['name_km'],
                'level' => config('app.position_level_deputy_head'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Unit
        $unit = [
            [
                'short_name' => 'RCU',
                'name_km' => 'ប្រធានផ្នែកគ្រប់គ្រងហានិភ័យនិងប្រតិបត្ដិតាម',
            ],
            [
                'short_name' => 'SDU',
                'name_km' => 'ប្រធានផ្នែកអភិវឌ្ឍកម្មវិធី',
            ],
            [
                'short_name' => 'NIU',
                'name_km' => 'ប្រធានផ្នែកបណ្ដាញនិងហេដ្ឋារចនាសម្ព័ន្ធ',
            ],
            [
                'short_name' => 'FAU',
                'name_km' => 'ប្រធានផ្នែកគណនេយ្យហិរញ្ញវត្ថុ',
            ],
            [
                'short_name' => 'AUM',
                'name_km' => 'ប្រធានផ្នែករដ្ឋបាល',
            ],
            [
                'short_name' => 'RUM',
                'name_km' => 'ប្រធានផ្នែកជ្រើសរើសបុគ្គលិក',
            ],
            [
                'short_name' => 'OAU',
                'name_km' => 'ប្រធានផ្នែកសវនកម្មប្រតិបត្តិការ',
            ],
            [
                'short_name' => 'ISS',
                'name_km' => 'ប្រធានផ្នែកលក់ធានារ៉ាប់រង',
            ],
            [
                'short_name' => 'THRU',
                'name_km' => 'ប្រធានផ្នែកបណ្ដុះបណ្ដាលនិងធនធានមនុស្ស',
            ],
            [
                'short_name' => 'DMM',
                'name_km' => 'ប្រធានផ្នែករចនានិងសរសេរអត្ថបទ',
            ],
            [
                'short_name' => 'PUM',
                'name_km' => 'ប្រធានផ្នែករដ្ឋបាលបុគ្គលិក',
            ],
            [
                'short_name' => 'WDU',
                'name_km' => 'ប្រធានផ្នែកអភិវឌ្ឍគេហទំព័រ',
            ],
            [
                'short_name' => 'MISU',
                'name_km' => 'ប្រធានផ្នែកបណ្តុះបណ្តាលនិងគ្រប់គ្រងប្រពន្ធ័',
            ],
        ];
        foreach ($unit as $item) {
            DB::table('positions')->insert([
                'short_name' => $item['short_name'],
                'name_km' => $item['name_km'],
                'level' => config('app.position_level_unit'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Senior
        $senior = [
            [
                'short_name' => 'SFA',
                'name_km' => 'មន្រ្ដីគណនេយ្យរហិរញ្ញវត្ថុជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SOA',
                'name_km' => 'សវនករប្រតិបត្តិការជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SPO',
                'name_km' => 'មន្រ្ដីរដ្ឋបាលបុគ្គលិកជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SCO',
                'name_km' => 'មន្រ្ដីឥណទានជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SCBO',
                'name_km' => 'មន្រ្ដីប្រព័ន្ធធនាគារស្នូលជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SOO',
                'name_km' => 'មន្រ្ដីប្រតិបត្ដិការជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SAO',
                'name_km' => 'មន្រ្ដីរដ្ឋបាលជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SRO',
                'name_km' => 'មន្រ្ថីជ្រើសរើសបុគ្គលិកជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SSO',
                'name_km' => 'មន្រ្ដីអភិវឌ្ឍកម្មវិធីជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SWDO',
                'name_km' => 'មន្ត្រីអភិវឌ្ឍគេហទំព័ជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SHRO',
                'name_km' => 'មន្ត្រីផ្នែកបណ្តុះបណ្តាលនិងធនធានមនុស្សជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'STO',
                'name_km' => 'មន្រ្ដីពន្ធជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SMDO',
                'name_km' => 'មន្ត្រីអភិវឌ្ឍកម្មវិធីទូរស័ព្ទជាន់ខ្ពស់',
            ],
            [
                'short_name' => 'SIA',
                'name_km' => 'មន្រ្ដីប្រឹក្សាធានារ៉ាប់រងជាន់ខ្ពស់',
            ],
        ];
        foreach ($senior as $item) {
            DB::table('positions')->insert([
                'short_name' => $item['short_name'],
                'name_km' => $item['name_km'],
                'level' => config('app.position_level_senior'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }


    }
}
