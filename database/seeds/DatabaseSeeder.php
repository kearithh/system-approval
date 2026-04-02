<?php

use App\Approve;
use App\RequestForm;
use App\RequestHR;
use App\RequestHRItem;
use App\RequestItem;
use App\RequestMemo;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(PositionsTableSeeder::class);
//        $this->call(UsersTableSeeder::class);
        $this->seedMemo();
//        $this->seedSpecialExpense();
//        $this->seedGeneralExpense();

    }

    public function seedMemo()
    {
        /**
         * Seed MEMO
         * 1. Store MEMO
         * 2. Store Approval
         */
        $memo = RequestMemo::create([
            'no' => 168,
            'title_en' => 'Loan Admin Fee Management Tool',
            'title_km' => 'ការដាក់ឲ្យប្រើប្រាស់នូវមុខងារគ្រប់គ្រងលើសេវាកម្មរដ្ឋបាល',
            'desc' => '
                <ul>
                    <li>មនុស្សទាំងអស់ កើតមកមានសេរីភាព និងសមភាព ក្នុងផ្នែកសេចក្ដីថ្លៃថ្នូរនិងសិទ្ធិ។ មនុស្ស មានវិចារណញ្ញាណនិងសតិសម្បជញ្ញៈជាប់ពីកំណើត ហើយគប្បីប្រព្រឹត្ដចំពោះគ្នាទៅវិញទៅមក ក្នុង ស្មារតីភាតរភាពជាបងប្អូន។</li>
                    <li>អន្ដរជាតិរបស់ប្រទេស ឬដែនដីដែលបុគ្គលណាម្នាក់រស់នៅ ទោះបីជាប្រទេស ឬដែនដីនោះឯករាជ្យក្ដី ស្ថិតក្រោមអាណាព្យាបាលក្ដី ឬគ្មានស្វ័យគ្រប់គ្រងក្ដី ឬស្ថិតក្រោមការដាក់ កម្រិតផ្សេងទៀតណាមួយ ដល់អធិបតេយ្យភាពក្ដី។</li>
                    <li>មនុស្សម្នាក់ៗ អាចប្រើប្រាស់សិទ្ធិនិងសេរីភាពទាំងអស់ ដែលមានចែងក្នុងសេចក្ដីប្រកាសនេះ ដោយគ្មានការប្រកាន់បែងចែកបែបណាមួយ មានជាអាទិ៍ ពូជសាសន៍ ពណ៌សម្បុរ ភេទ ភាសា សាសនា មតិនយោបាយ ឬមតិផ្សេងៗទៀត ដើមកំណើតជាតិ ឬសង្គម ទ្រព្យសម្បត្ដិ កំណើត ឬស្ថានភាព ដទៃៗទៀតឡើយ។</li>
                </ul>
            ',
            'point' => json_encode([
                'មនុស្សទាំងអស់ កើតមកមានសេរីភាព និងសមភាព ក្នុងផ្នែកសេចក្ដីថ្លៃថ្នូរនិងសិទ្ធិ។ មនុស្ស មានវិចារណញ្ញាណនិងសតិសម្បជញ្ញៈជាប់ពីកំណើត ហើយគប្បីប្រព្រឹត្ដចំពោះគ្នាទៅវិញទៅមក ក្នុង ស្មារតីភាតរភាពជាបងប្អូន។',
                'មនុស្សម្នាក់ៗ អាចប្រើប្រាស់សិទ្ធិនិងសេរីភាពទាំងអស់ ដែលមានចែងក្នុងសេចក្ដីប្រកាសនេះ ដោយគ្មានការប្រកាន់បែងចែកបែបណាមួយ មានជាអាទិ៍ ពូជសាសន៍ ពណ៌សម្បុរ ភេទ ភាសា សាសនា មតិនយោបាយ ឬមតិផ្សេងៗទៀត ដើមកំណើតជាតិ ឬសង្គម ទ្រព្យសម្បត្ដិ កំណើត ឬស្ថានភាព ដទៃៗទៀតឡើយ។',
                'អន្ដរជាតិរបស់ប្រទេស ឬដែនដីដែលបុគ្គលណាម្នាក់រស់នៅ ទោះបីជាប្រទេស ឬដែនដីនោះឯករាជ្យក្ដី ស្ថិតក្រោមអាណាព្យាបាលក្ដី ឬគ្មានស្វ័យគ្រប់គ្រងក្ដី ឬស្ថិតក្រោមការដាក់ កម្រិតផ្សេងទៀតណាមួយ ដល់អធិបតេយ្យភាពក្ដី។'
            ], JSON_UNESCAPED_UNICODE),
            'start_date' => Carbon::now(),
            'status' => config('app.approve_status_draft'),
            'user_id' => 7,
        ]);

        // Store Approval
        $HHR = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HHR')->first();
        $HIA = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HIA')->first();
        $DHBO = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'DHBO')->first();
        $reviewers = [
            'head_hr' => $HHR->id,
            'head_audit' => $HIA->id,
            'dceo' => $DHBO->id,
            'ceo' => getCEO()->id,
        ];
        foreach ($reviewers as $key => $item) {
            Approve::create([
                'created_by' => 7,
                'status' => config('app.approve_status_draft'),
                'request_id' => $memo->id,
                'type' => config('app.type_memo'),
                'reviewer_position_id' => null,
                'position' => $key,
                'reviewer_id' => $item,
            ]);
        }
        $this->command->info('1 MEMO is created...');
    }

    //////////////////////////////////////////
    public function seedSpecialExpense()
    {
        $expenseParam = [
            'user_id' => 7,
            'purpose' => 'សំណើសុំការចំណាយដើម្បីទិញឈ្មោះ Domain ៖ www.madamenom.com ។',
            'reason' => 'ដើម្បីប្រើប្រាស់បង្កើត Website, Email, និងផ្សេងៗដែលពាក់ព័ន្ធនឹង Madame Nom ។',
            'total_amount' => '13.189',
            'created_by' => 7,
            'draft' => 0,
            'status' => config('app.approve_status_draft'),
        ];
        $expense =  new RequestForm($expenseParam);
        $expense->save();

        // Store request item
        $itemParam = [
            'request_id' => $expense->id,
            'name' => 'Domain Name',
            'desc' => 'website, email and others.',
            'qty' => 1,
            'unit_price' => 11.99,
            'vat' => 10,
            'remark' => 'For 1 Year',
            'amount' => 13.189,
            ];
        $expenseItem = new RequestItem($itemParam);
        $expenseItem->save();

        // Store Approval
        $HFN = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HFN')->first();
        $reviewerId = array_merge([$HFN->id], [getCEO()->id]);
        foreach ($reviewerId as $item) {
            Approve::create([
                'created_by' => 7,
                'status' => config('app.approve_status_draft'),
                'request_id' => $expense->id,
                'type' => config('app.type_special_expense'),
                'reviewer_id' => $item,
            ]);
        }
        $this->command->info('1 Special Expense is created...');
    }

    public function seedGeneralExpense()
    {
        $data = [
            'user_id' => 7,
            'total' => (10*450) + (10*700) + (10*45.5),
            'status' => config('app.approve_status_draft'),
            'created_by' => 7,
        ];
        $requestHR = new RequestHR($data);
        $requestHR->save();

        // Store Item
        $itemData = [
            [
                'name' => '',
                'desc' => 'ទិញម៉ាស៊ីន​Laptop ថ្មី សម្រាប់បុគ្គលិកចូលថ្មី',
                'purpose' => 'សម្រាប់ឲ្របុគ្គលិកថ្មីប្រើប្រាសប្រចាំថ្ងៃ',
                'qty' => 10,
                'unit' => 'Unit',
                'unit_price' => 450,
            ],
            [
                'name' => '',
                'desc' => 'ទិញតុ ថ្មី សម្រាប់បុគ្គលិកចូលថ្មី',
                'purpose' => 'សម្រាប់ឲ្របុគ្គលិកថ្មីប្រើប្រាសប្រចាំថ្ងៃ',
                'qty' => 10,
                'unit' => 'Unit',
                'unit_price' => 70,
            ]
            ,
            [
                'name' => '',
                'desc' => 'ទិញកៅអី ថ្មី សម្រាប់បុគ្គលិកចូលថ្មី',
                'purpose' => 'សម្រាប់ឲ្របុគ្គលិកថ្មីប្រើប្រាសប្រចាំថ្ងៃ',
                'qty' => 10,
                'unit' => 'Unit',
                'unit_price' => 45.5,
            ]
        ];
        foreach ($itemData as $key => $item) {
            RequestHRItem::create([
                'request_id' => $requestHR->id,
                'name' => $item['name'],
                'desc' => $item['desc'],
                'purpose' => $item['purpose'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        // Store Approval
        $HHR = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HHR')->first();
        $HFN = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HFN')->first();
        $HIA = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'HIA')->first();
        $DHBO = User::join('positions', 'users.position_id', '=', 'positions.id')->where('positions.short_name', 'DHBO')->first();

        $reviewers = [
            'unit_manager' => 7,
            'head_department' => $HHR->id,
            'chief_executive' => 8,
            'finance' => $HFN->id,
            'reviewer' => $HIA->id,
            'assistant_ceo' => $DHBO->id,
            'ceo' => getCEO()->id,
        ];
        foreach ($reviewers as $key => $item) {
            Approve::create([
                'created_by' => 7,
                'status' => 1, // 2 = approve, 1 = draft, 3 = reject
                'request_id' => $requestHR->id,
                'type' => 4, // HR Expense
                'reviewer_position_id' => null,
                'position' => $key,
                'reviewer_id' => $item,
            ]);
        }
        $this->command->info('1 General Expense is created...');
    }
}
