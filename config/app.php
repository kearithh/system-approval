<?php

return [

    'branch_approver'               => 'phatsaomony',
    'branch_general_approver'       => 'chaysophal',
    'mmi_approver'                  => 'kounlinna',

    'special_short_sign_mmi'        => '398', // id user vatanak

    'is_verify'                     => 0, // 1 use; 0 not
    'verify_id'                     => 39, // id user to verify cio (LK bunthach)

    'is_verify_report'              => 0, // 1 use; 0 not
    'verify_report_id'              => 33, // id user to verify of menu report a president (NK somean)

    'is_order_approver'             => 0, // 1 use; 0 not

    'system_admin_role'             => 1, // manage all and can see all request
    'system_sub_admin_role'         => 2, // manage all and can't see all request
    'system_manager_role'           => 3, // can see summary report
    'system_user_role'              => 0, // request only 

    'is_use_group_support'          => 0, // 1 use; 0 not

    'type_special_expense'          => 1, // special expense
    'type_memo'                     => 2, // memo
    'type_dispose'                  => 3, // Will update to type_disposal
    'type_general_expense'          => 4, // general expense
    'type_disposal'                 => 5, // disposal asset
    'type_damaged_log'              => 6, // damaged asset
    'type_hr_request'               => 7, // request letter
    'type_loans'                    => 8, // loan approval
    'type_sale_asset'               => 9, // sale asset
    'type_return_budget'            => 10, // return budget
    'type_send_receive'             => 11, // send-receive asset
    'type_transfer_asset'           => 12, // transfer asset 
    'type_reschedule_loan'          => 13, // loan adjusment
    'type_mission'                  => 14, // mission
    'type_mission_item'             => 15, // mission item
    'type_training'                 => 16, // training
    'type_request_ot'               => 17, // request OT
    'type_penalty'                  => 18, // wave panalty
    'type_cutting_interest'         => 19, // cutting interest
    'type_employee_penalty'         => 20, // employee penalty
    'type_cash_advance'             => 21, // cash advance 
    'type_resign'                   => 22, // resign letter 
    'type_general_request'          => 23, // branch request (fn) 
    'type_wave_association'         => 24, // wave association 
    'type_request_create_user'      => 25, // request create user
    'type_association'              => 26, // association
    'type_survey_report'            => 27, // survey report
    'type_custom_letter'            => 28, // custom letter
    'type_policy'                   => 29, // policy
    'type_request_disable_user'     => 30, // request disable user
    'type_borrowing_loan'           => 31, // borrowing loan
    'type_wc_request'               => 32, // withdrawal collateral

    'type_village_loan'             => 33, // village loan
    'type_mission_clearance'        => 34, // mission clearance
    'type_request_gasoline'         => 35, // request gasoline
    'type_pr_request'               => 36, // pr request
    'type_po_request'               => 37, // po request
    'type_grn'                      => 38, //grn

    'type_setting_approver'         => 100, // request setting approver

    'max_mmi_ceo_approve_expense'   => 1000,

    'user_active'       => 1,
    'user_inactive'     => 0,

    'approve_status_draft'      => 1,
    'approve_status_approve'    => 2,
    'approve_status_reject'     => 3,
    'approve_status_save'       => 4,
    'approve_status_disable'    => 5,
    'approve_status_delete'     => 4,

    'draft'             => 'draft',
    'pending'           => 'pending',
    'reviewing'         => 'reviewing',
    'reviewed'          => 'reviewed',
    'approved'          => 'approved',
    'rejected'          => 'rejected',

    // Request type as string
    'special_expense' => 'special_expense',
    'pr_request' => 'pr_request',
    'general_expense' => 'general_expense',
    'memo' => 'memo',
    'disposal' => 'disposal',
    'damaged_log' => 'damaged_log',
    'waive_penalty' => 'waive_penalty',
    'report' => 'report',
    'announcement' => 'announcements', // សេចក្ដីប្រកាស សេចក្តីណែនាំ
    'loan_request' => 'loan_request',

    // Setting name
    'approver_setting_report'   => 'approver_setting_report',

    'send_to_requester'         => 1,
    'not_send_to_requester'     => 2,

    // clear cash advance
    'cash_clear'        => 1,
    'cash_not_clear'    => 0,

    'advance'           => 1,
    'clear_advance'     => 2,
    'reimbursement'     => 3,

    // set types for resigns
    'resign_letter' => 1, // លិខិតលាឈប់ពីការងារបុគ្គលិក, 
    'resign_last_day'  => 2, // សំណើអនុញ្ញាតលាឈប់ពីការងារបុគ្គលិក,    

    'position_level_president'              => 1,
    'position_level_assistant_president'    => 2,
    'position_level_deputy_president'       => 3,
    'position_level_ceo'                    => 10,
    'position_level_assistant_ceo'          => 20,
    'position_level_deputy_ceo'             => 30,
    'position_level_chef'                   => 40,
    'position_level_head'                   => 50,
    'position_level_deputy_head'            => 60,
    'position_level_unit'                   => 70,
    'position_level_senior'                 => 80,
    'position_level_officer'                => 90,
    'position_level_other'                  => 100,
    'position_level_other_ord'              => 500,

    'position_level_gm'             => 11,
    'position_level_rm'             => 52,
    'position_level_acting_head'    => 51,
    'position_level_om'             => 53,
    'position_level_aom'            => 54,


    'position_level_pm'             => 200,
    'position_level_bm'             => 210,
    'position_level_abm'            => 220,
    'position_level_dbm'            => 230,
    'position_level_ba'             => 240,
    'position_level_bc'             => 250,
    'position_level_bt'             => 260,


    'tags' => [
        (object)[
            'name' => 'Daily',
            'name_km' => 'ប្រចាំថ្ងៃ',
            'slug' => 'daily',
        ],
        (object)[
            'name' => 'Weekly',
            'name_km' => 'ប្រចាំសប្ដាហ៍',
            'slug' => 'weekly',
        ],
        (object)[
            'name' => 'Monthly',
            'name_km' => 'ប្រចាំខែ',
            'slug' => 'monthly',
        ],
        (object)[
            'name' => 'Quarterly',
            'name_km' => 'ប្រចាំត្រីមាស',
            'slug' => 'quarterly',
        ],
        (object)[
            'name' => 'Yearly',
            'name_km' => 'ប្រចាំឆ្នាំ',
            'slug' => 'yearly',
        ],
//        (object)[
//            'name' => 'New Report',
//            'name_km' => 'របាយការណ៍ថ្មី',
//            'slug' => 'new_report',
//        ]
    ],

//type of national id
    'id_types' => [
        (object)[
            'name' => 'Birth Certificate',
            'name_km' => 'សំបុត្រកំណើត',
        ],
        (object)[
            'name' => 'Family Book',
            'name_km' => 'សៀវភៅគ្រួសារ',
        ],
        (object)[
            'name' => 'Married Certificate',
            'name_km' => 'សំបុត្រអាពាហ៍ពិពាហ៍',
        ],
        (object)[
            'name' => 'National ID',
            'name_km' => 'អត្តសញ្ញាណប័ណ្ណ',
        ],
        (object)[
            'name' => 'Passport',
            'name_km' => 'លិខិតឆ្លងដែន',
        ],
        (object)[
            'name' => 'Residence Book',
            'name_km' => 'សៀវភៅស្នាក់នៅ',
        ],
        (object)[
            'name' => 'Residence Certificate',
            'name_km' => 'លិខិតបញ្ជាក់ទីលំនៅ',
        ],
        (object)[
            'name' => 'Others',
            'name_km' => 'ផ្សេងៗ',
        ],
    ],


    'request_types' => [
        (object)[
            'id' => '1',
            'name' => 'Special Expense',
            'name_km' => 'ចំណាយពិសេស',
            'table' => '',
        ],
        (object)[
            'id' => '2',
            'name' => 'Memo',
            'name_km' => 'អនុសរណៈ',
        ],
        // (object)[
        //     'id' => '3',
        //     'name' => 'Dispose',
        //     'name_km' => 'លុបសម្ភារៈ',
        //     'table' => '',
        // ],
        (object)[
            'id' => '4',
            'name' => 'General Expense',
            'name_km' => 'ចំណាយទូទៅ',
            'table' => '',
        ],
        (object)[
            'id' => '5',
            'name' => 'Disposal',
            'name_km' => 'លុបសម្ភារៈ',
            'table' => '',
        ],
        (object)[
            'id' => '6',
            'name' => 'Damaged Asset',
            'name_km' => 'សម្ភារៈខូច',
            'table' => '',
        ],
        (object)[
            'id' => '7',
            'name' => 'Letter',
            'name_km' => 'លិខិត',
            'table' => '',
        ],
        (object)[
            'id' => '8',
            'name' => 'Loan',
            'name_km' => 'ឥណទាន',
            'table' => '',
        ],
        (object)[
            'id' => '9',
            'name' => 'Sale Asset',
            'name_km' => 'លក់សម្ភារៈ',
            'table' => '',
        ],
        (object)[
            'id' => '10',
            'name' => 'Return Budget',
            'name_km' => 'ប្រគល់ថវិកា',
            'table' => '',
        ],
        (object)[
            'id' => '11',
            'name' => 'Send Receive',
            'name_km' => 'ប្រគល់ទទួល',
            'table' => '',
        ],
        (object)[
            'id' => '12',
            'name' => 'Transfer Asset',
            'name_km' => 'ផ្ទេរសម្ភារៈ',
            'table' => '',
        ],
        (object)[
            'id' => '13',
            'name' => 'Loan Adjustment',
            'name_km' => 'កែប្រែតារាងកាលវិភាគសងប្រាក់',
            'table' => '',
        ],
        (object)[
            'id' => '14',
            'name' => 'Mission',
            'name_km' => 'បេសកកម្ម',
            'table' => '',
        ],
        // (object)[
        //     'id' => '15',
        //     'name' => 'Mission Item',
        //     'name_km' => 'បេសកកម្ម',
        //     'table' => '',
        // ],
        (object)[
            'id' => '16',
            'name' => 'Training',
            'name_km' => 'បណ្តុះបណ្តាល',
            'table' => '',
        ],
        (object)[
            'id' => '17',
            'name' => 'OT',
            'name_km' => 'ថែមម៉ោង',
            'table' => 'request_ot',
        ],
        (object)[
            'id' => '18',
            'name' => 'Penalty',
            'name_km' => 'សុំកាត់ប្រាក់ពិន័យ',
            'table' => '',
        ],
        (object)[
            'id' => '19',
            'name' => 'Cutting Interest',
            'name_km' => 'កាត់ការប្រាក់',
            'table' => '',
        ],
        (object)[
            'id' => '20',
            'name' => 'Employee Penalty Return',
            'name_km' => 'ទទួលប្រាក់ពិន័យបុគ្គលិក',
            'table' => '',
        ],
        (object)[
            'id' => '21',
            'name' => 'Cash Advance',
            'name_km' => 'បុរេប្រទាន',
            'table' => '',
        ],
        (object)[
            'id' => '22',
            'name' => 'Resign Letter',
            'name_km' => 'លិខិតលាឈប់ពីការងារ',
            'table' => '',
        ],
        (object)[
            'id' => '23',
            'name' => 'Branch Request(FN)',
            'name_km' => 'សំណើពីសាខា',
            'table' => '',
        ],
        (object)[
            'id' => '24',
            'name' => 'Wave Association',
            'name_km' => 'អត់លក់សេវាសង្រោះគ្រួសារ',
            'table' => '',
        ],
        (object)[
            'id' => '25',
            'name' => 'Request Create User',
            'name_km' => 'សុំបង្កើតឈ្មោះអ្នកប្រើប្រាស់ប្រព័ន្ធ',
            'table' => '',
        ],
        (object)[
            'id' => '36',
            'name' => 'PR Request',
            'name_km' => 'សំណើរបញ្ជាទិញ',
            'table' => '',
        ],
        (object)[
            'id' => '37',
            'name' => 'PO Request',
            'name_km' => 'បណ្ណបញ្ជាទិញ',
            'table' => '',
        ],
        (object)[
            'id' => '38',
            'name' => 'GRN',
            'name_km' => 'បណ្ណទទួលទំនិញ​',
            'table' => '',
        ],
    ],

    'types_request_user' => [
        'កុំព្យូទ័រ',
        'អ៊ីមែល',
        'ផ្សេងៗ...........'
    ],

    'types_request_user_skp' => [
        'ប្រព័ន្ធធនាគារស្នូល',
        'កុំព្យូទ័រ',
        'អ៊ីមែល',
        'ផ្សេងៗ...........'
    ],

    'types_request_user_mmi' => [
        'ប្រព័ន្ធគ្រប់គ្រងធានារ៉ាប់រងខ្នាតតូច',
        'កុំព្យូទ័រ',
        'អ៊ីមែល',
        'ផ្សេងៗ...........'
    ],

    'customer_title' => [
        'លោក',
        'លោកស្រី',
        'អ្នកនាង',
        'កញ្ញា',
        'លោកឧកញ៉ា',
        'អ្នកឧកញ៉ា',
        'ឯកឧត្ដម',
        'លោកជំទាវ'
    ],

    'types_disable_user' => [
        0 => 'លាលែង / លាឈប់ពីតំណែង',
        1 => 'ចូលនិវត្តន៍',
        2 => 'ឈប់ពីការងារ',
        3 => 'មូលហេតុផ្សេងៗ',
    ],

    'branch_request' => [
        1 => 'សំណើខ្ចប់សាច់ប្រាក់',
        2 => 'សំណើសុំរក្សាសាច់ប្រាក់ទុកលើគោលការណ៍',
        3 => 'សំណើសុំប្តូរប្រាក់',
        4 => 'សំណើសុំចំណាយប្រចាំថ្ងៃ',
        5 => 'សំណើសុំទទួលប្រាក់ពិន័យបុគ្គលិក',
        6 => 'សំណើសុំព្យួរលុយលើស ឬលុយបាត់',
    ],

    'letter_request' => [
        0 => 'សុំតម្លើងប្រាក់ម៉ោងបុគ្គលិក | Request for increase in staff hours',
        1 => 'សុំតែងតាំងបុគ្គលិក​ | Apply for staff',
        2 => 'សុំផ្លាស់ប្ដូរទីតាំងបុគ្គលិក | Request to change staff location',
        3 => 'សំណើសុំបុគ្គលិក | NEW HIRED STAFF REQUEST',
        4 => 'សុំផ្លាស់ប្ដូរតួនាទីបុគ្គលិក | Request to change staff role',
        5 => 'សុំតម្លើងប្រាក់បៀវត្សរ៍បុគ្គលិក | Request for salary increase',
        6 => 'សុំផ្លាស់ប្ដូរម៉ោងការងារបុគ្គលិក | Request to change staff working hours',
    ],

    'type_survey' => [
        1 => 'បានធ្វើ',
        2 => 'មិនបានធ្វើ',
        3 => 'មានបញ្ហា',
    ],

    'bias_type' => [
        (object)[
            'val' => '1',
            'name_en' => 'Increase',
            'name_km' => 'កើន',
        ],
        (object)[
            'val' => '0',
            'name_en' => 'Impartial',
            'name_km' => 'ស្មើរ',
        ],
        (object)[
            'val' => '-1',
            'name_en' => 'Decrease',
            'name_km' => 'ថយ',
        ],
    ],

    'benefit_type' => [
        (object)[
            'val' => '1',
            'name_en' => 'Holiday',
            'name_km' => 'ថ្ងៃឈប់សម្រាក(បុណ្យជាតិ)',
        ],
        (object)[
            'val' => '2',
            'name_en' => 'Weekend',
            'name_km' => 'ថ្ងៃឈប់សម្រាក(ចុងសប្តាហ៍)',
        ],
        (object)[
            'val' => '3',
            'name_en' => 'Working Day',
            'name_km' => 'ថ្ងៃធ្វើការ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Force Update Password
    |--------------------------------------------------------------------------
    |
    */

    'force_update_password' => env('FORCE_UPDATE_PASSWORD', 0),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Phnom_Penh',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'km',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Telegram\Bot\Laravel\TelegramServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

//        Barryvdh\DomPDF\ServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
//        Elibyy\TCPDF\ServiceProvider::class,
//        setasign\Fpdi\Fpdi::class,
        'Barryvdh\Debugbar\ServiceProvider',

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

//        'PDF' => Elibyy\TCPDF\Facades\TCPDF::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Telegram' => Telegram\Bot\Laravel\Facades\Telegram::class,
        'Debugbar' => 'Barryvdh\Debugbar\Facade',

    ],


];
