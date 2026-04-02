<?php

$menu[1] = [
    'text' => 'Dashboard',
    'url'  => 'dashboard',
    'icon' => 'fas fa-fw fa-tachometer-alt',
];

$menu[2] = [
    'text' => 'Create Request',
    'url'  => '#',
    'icon' => 'fas fa-fw fa-paper-plane',
    'submenu' => [
        [
            'text' => 'Memo',
            'url'  => 'request_memo/create',
            'icon' => 'fas fa-plus',
        ],
        [
            'text' => 'Letter',
            'url'  => 'hr_request/create',
            'icon' => 'fas fa-plus',
            'label' => 1,
            'name' => '',
        ],
        [
            'text' => 'Special Expense',
            'url'  => 'request/create',
            'icon' => 'fas fa-plus',
            'class' => 'create_request'
        ],
        [
            'text' => 'General Expense',
            'url'  => 'request_hr/create',
            'icon' => 'fas fa-plus',
            'class' => 'create_request_hr'
        ],
        [
            'text' => 'Disposal Asset',
            'url'  => 'disposal/create',
            'icon' => 'fas fa-plus',
        ],
        [
            'text' => 'Damaged Asset',
            'url'  => 'damagedlog/create',
            'icon' => 'fas fa-plus',
        ],
//        [
//            'text' => 'Sale Asset',
//            'url'  => 'sale_asset/create',
//            'icon' => 'fas fa-plus',
//        ],
//        [
//            'text' => 'Return Budget',
//            'url'  => 'return_budget/create',
//            'icon' => 'fas fa-plus',
//        ],
//        [
//            'text' => 'Send-Receive Asset',
//            'url'  => 'send_receive/create',
//            'icon' => 'fas fa-plus',
//        ],
//        [
//            'text' => 'Loan Request',
//            'url'  => 'loan/create',
//            'icon' => 'fas fa-plus',
//        ],
        [
            'text' => 'Report',
            'url'  => 'group_request/item/create',
            'icon' => 'fas fa-plus',
        ],

    ]
];

// $menu[3] = [
//     'text' => 'Pending List',
//     'url'  => '#',
//     'icon' => 'fa fa-bars',
//     'name' => '',
//     'submenu' => [
//         [
//             'text' => 'Memo',
//             'url'  => 'pending/memo',
//             'icon' => 'fas fa-gavel',
//             'label' => 1,
//             'name' => '',
//         ],
//         [
//             'text' => 'Special Expense',
//             'url'  => 'pending/special-expense',
//             'icon' => 'fas fa-star',
//             'class' => 'create_request',
//             'label' => 1,
//             'name' => '',
//         ],
//         [
//             'text' => 'General Expense',
//             'url'  => 'pending/general-expense',
//             'icon' => 'fas fa-money-check-alt',
//             'class' => 'create_request_hr',
//             'label' => 1,
//             'name' => '',
//         ],
//         [
//             'text' => 'Disposal',
//             'url'  => 'pending/disposal',
//             'icon' => 'fas fa-minus-circle',
//             'label' => 1,
//             'name' => '',
//         ],
//         // [
//         //     'text' => 'Damaged Asset',
//         //     'url'  => 'pending/damagedlog',
//         //     'icon' => 'fas fa-exclamation-circle',
//         //     'label' => 1,
//         //     'name' => '',
//         // ],
//         // [
//         //     'text' => 'HR Request',
//         //     'url'  => 'pending/hr_request',
//         //     'icon' => 'fas fa-user-shield',
//         //     'label' => 1,
//         //     'name' => '',
//         // ],
//     ]
// ];

$menu[11] = [
    'text' => 'Pending List',
    'url'  => '#',
    'icon' => 'fa fa-bars',
    'name' => '',
    'status' => 'pending',
    'submenu' => [
        [
            'text' => 'STSK',
            'url'  => 'pending?company=STSK',
            'name' => 'stsk_pending',
        ],
        [
            'text' => 'MFI',
            'url'  => 'pending?company=MFI',
            'name' => 'mfi_pending',
        ],
        [
            'text' => 'NGO',
            'url'  => 'pending?company=NGO',
            'name' => 'ngo_pending',
        ],
        [
            'text' => 'ORD',
            'url'  => 'pending?company=ORD',
            'name' => 'ord_pending',
        ],
        [
            'text' => 'ST',
            'url'  => 'pending?company=ST',
            'name' => 'st_pending',
        ],
        [
            'text' => 'MMI',
            'url'  => 'pending?company=MMI',
            'name' => 'mmi_pending',
        ],
        [
            'text' => 'MHT',
            'url'  => 'pending?company=MHT',
            'name' => 'mht_pending',
        ],
        [
            'text' => 'TSP',
            'url'  => 'pending?company=TSP',
            'name' => 'tsp_pending',
        ],
    ]
];

$menu[8] = [
    'text' => 'To Approve List',
    'url'  => '#',
    'icon' => 'fa fa-check',
    'name' => 'open_list1',
    'status' => 'toapprove',
    'submenu' => [
        [
            'text' => 'STSK',
            'url'  => 'toapprove?company=STSK',
            'name' => 'stsk_approval',
        ],
        [
            'text' => 'MFI',
            'url'  => 'toapprove?company=MFI',
            'name' => 'mfi_approval',
        ],
        [
            'text' => 'NGO',
            'url'  => 'toapprove?company=NGO',
            'name' => 'ngo_approval',
        ],
        [
            'text' => 'ORD',
            'url'  => 'toapprove?company=ORD',
            'name' => 'ord_approval',
        ],
        [
            'text' => 'ST',
            'url'  => 'toapprove?company=ST',
            'name' => 'st_approval',
        ],
        [
            'text' => 'MMI',
            'url'  => 'toapprove?company=MMI',
            'name' => 'mmi_approval',
        ],
        [
            'text' => 'MHT',
            'url'  => 'toapprove?company=MHT',
            'name' => 'mht_approval',
        ],
        [
            'text' => 'TSP',
            'url'  => 'toapprove?company=TSP',
            'name' => 'tsp_approval',
        ],
    ]
];

//    $menu[4] = [
//        'text' => 'To Approve List',
//        'url'  => '#',
//        'icon' => 'fa fa-check',
//        'name' => 'open_list',
//        'submenu' => [
//            [
//                'text' => 'Memo',
//                'url'  => 'approval/memo',
//                'icon' => 'fas fa-gavel',
//                'label' => 1,
//                'name' => 'memo_approval',
//            ],
//            [
//                'text' => 'Special Expense',
//                'url'  => 'approval/special-expense',
//                'icon' => 'fas fa-star',
//                'class' => 'create_request',
//                'label' => 1,
//                'name' => 'se_approval',
//            ],
//            [
//                'text' => 'General Expense',
//                'url'  => 'approval/general-expense',
//                'icon' => 'fas fa-money-check-alt',
//                'class' => 'create_request_hr',
//                'label' => 1,
//                'name' => 'ge_approval',
//            ],
//            [
//                'text' => 'Disposal',
//                'url'  => 'approval/disposal',
//                'icon' => 'fas fa-minus-circle',
//                'label' => 1,
//                'name' => 'disposal_approval',
//            ],
// [
//     'text' => 'Damaged Asset',
//     'url'  => 'approval/damagedlog',
//     'icon' => 'fas fa-exclamation-circle',
//     'label' => 1,
//     'name' => '',
// ],
// [
//     'text' => 'HR Request',
//     'url'  => 'approval/hr_request',
//     'icon' => 'fas fa-user-shield',
//     'label' => 1,
//     'name' => '',
// ],
//        ]
//    ];

$menu[9] = [
    'text' => 'Rejected/Commented List',
    'url'  => '#',
    'icon' => 'fa fa-times',
    'name' => 'open_rejected1',
    'status' => 'reject',
    'submenu' => [
        [
            'text' => 'STSK',
            'url'  => 'reject?company=STSK',
            'name' => 'stsk_rejected',
        ],
        [
            'text' => 'MFI',
            'url'  => 'reject?company=MFI',
            'name' => 'mfi_rejected',
        ],
        [
            'text' => 'NGO',
            'url'  => 'reject?company=NGO',
            'name' => 'ngo_rejected',
        ],
        [
            'text' => 'ORD',
            'url'  => 'reject?company=ORD',
            'name' => 'ord_rejected',
        ],
        [
            'text' => 'ST',
            'url'  => 'reject?company=ST',
            'name' => 'st_rejected',
        ],
        [
            'text' => 'MMI',
            'url'  => 'reject?company=MMI',
            'name' => 'mmi_rejected',
        ],
        [
            'text' => 'MHT',
            'url'  => 'reject?company=MHT',
            'name' => 'mht_rejected',
        ],
        [
            'text' => 'TSP',
            'url'  => 'reject?company=TSP',
            'name' => 'tsp_rejected',
        ],
    ]
];

//    $menu[5] = [
//        'text' => 'Rejected/Commented List',
//        'url'  => '#',
//        'icon' => 'fa fa-times',
//        'name' => 'open_rejected',
//        'submenu' => [
//            [
//                'text' => 'Memo',
//                'url'  => 'reject/memo',
//                'icon' => 'fas fa-gavel',
//                'label' => 1,
//                'name' => 'reject_memo_approval',
//            ],
//            [
//                'text' => 'Special Expense',
//                'url'  => 'reject/special-expense',
//                'icon' => 'fas fa-star',
//                'class' => 'create_request',
//                'label' => 1,
//                'name' => 'reject_se_approval',
//            ],
//            [
//                'text' => 'General Expense',
//                'url'  => 'reject/general-expense',
//                'icon' => 'fas fa-money-check-alt',
//                'class' => 'create_request_hr',
//                'label' => 1,
//                'name' => 'reject_ge_approval',
//            ],
//            [
//                'text' => 'Disposal',
//                'url'  => 'reject/disposal',
//                'icon' => 'fas fa-minus-circle',
//                'label' => 1,
//                'name' => 'reject_disposal_approval',
//            ],
// [
//     'text' => 'Damaged Asset',
//     'url'  => 'reject/damagedlog',
//     'icon' => 'fas fa-exclamation-circle',
//     'label' => 1,
//     'name' => '',
// ],
// [
//     'text' => 'HR Request',
//     'url'  => 'reject/hr_request',
//     'icon' => 'fas fa-user-shield',
//     'label' => 1,
//     'name' => '',
// ],
//        ]
//    ];

$menu[10] = [
    'text' => 'Approved List',
    'url'  => '#',
    'icon' => 'fa fa-check-circle',
    'name' => 'open_approved1',
    'status' => 'approved',
    'submenu' => [
        [
            'text' => 'STSK',
            'url'  => 'approved?company=STSK',
            'name' => 'stsk_approved',
        ],
        [
            'text' => 'MFI',
            'url'  => 'approved?company=MFI',
            'name' => 'mfi_approved',
        ],
        [
            'text' => 'NGO',
            'url'  => 'approved?company=NGO',
            'name' => 'ngo_approved',
        ],
        [
            'text' => 'ORD',
            'url'  => 'approved?company=ORD',
            'name' => 'ord_approved',
        ],
        [
            'text' => 'ST',
            'url'  => 'approved?company=ST',
            'name' => 'st_approved',
        ],
        [
            'text' => 'MMI',
            'url'  => 'approved?company=MMI',
            'name' => 'mmi_approved',
        ],
        [
            'text' => 'MHT',
            'url'  => 'approved?company=MHT',
            'name' => 'mht_approved',
        ],
        [
            'text' => 'TSP',
            'url'  => 'approved?company=TSP',
            'name' => 'tsp_approved',
        ],
    ]
];

//    $menu[6] = [
//        'text' => 'Approved List',
//        'url'  => 'dashboard',
//        'icon' => 'fas fa-fw fa-check-circle',
//        'name' => 'open_approved',
//        'submenu' => [
//            [
//                'text' => 'Memo',
//                'url'  => 'request_memo?status=2',
//                'icon' => 'fas fa-fw fa-list',
//            ],
//            [
//                'text' => 'Special Expense',
//                'url'  => 'request?status=2',
//                'icon' => 'fas fa-fw fa-list',
//            ],
//            [
//                'text' => 'General Expense',
//                'url'  => 'request_hr?status=2',
//                'icon' => 'fas fa-fw fa-list',
//            ],
//            [
//                'text' => 'Disposal',
//                'url'  => 'disposal?status=2',
//                'icon' => 'fas fa-fw fa-list',
//            ],
// [
//     'text' => 'Damaged Asset',
//     'url'  => 'damagedlog?status=2',
//     'icon' => 'fas fa-fw fa-list',
// ],
// [
//     'text' => 'HR Request',
//     'url'  => 'hr_request?status=2',
//     'icon' => 'fas fa-fw fa-list',
// ],
//        ]
//    ];

$menu[7] = [
    'text' => 'Setting',
    'url'  => '#',
    'icon' => 'fas fa-fw fa-sliders-h',
    'submenu' => [
        [
            'text' => 'Staff',
            'url'  => 'user',
            'icon' => 'fas fa-fw fa-user',
        ],
        [
            'text' => 'Company',
            'url'  => 'company',
            'icon' => 'fas fa-building',
        ],
        [
            'text' => 'Position',
            'url'  => 'position',
            'icon' => 'fas fa-fw fa-crosshairs',
        ],
        [
            'text' => 'Branch',
            'url'  => 'branch',
            'icon' => 'fas fa-code-branch',
        ],
        [
            'text' => 'Department',
            'url'  => 'department',
            'icon' => 'fas fa-building',
        ],
        // [
        //     'text' => 'Reviewer',
        //     'url'  => 'reviewer',
        //     'icon' => 'fas fa-check-double',
        // ],
    ]
];
//    $menu[11] = [
//        'text' => 'Reports',
//        'url'  => 'group_request',
//        'icon' => 'fa fa-chart-area',
//    ];

$menu[12] = [
    'text' => 'Summary Report',
    'url'  => '#',
    'icon' => 'fas fa-fw fa-chart-bar',
    'submenu' => [
        [
            'text' => 'Special Expense',
            'url'  => 'summary_report/special-expense',
            'icon' => 'fas fa-star',
        ],
        [
            'text' => 'General Expense',
            'url'  => 'summary_report/general-expense',
            'icon' => 'fas fa-money-check-alt',
        ],
        // [
        //     'text' => 'Memo',
        //     'url'  => 'summary_report/memo',
        //     'icon' => 'fas fa-gavel',
        // ],
    ]
];

return [

    'menu' => $menu,

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'E-Approval',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-logo
    |
    */

    'logo' => '<b>E-APPROVAL</b>',
    'login_logo' => '<img src="img/ord_logo.png" alt="" style="width: 120px"><br><b>E-Approval</b>',
    'logo_img' => 'img/ord_logo.png',
    'logo_img_ord' => 'img/ord.jpg',
    'logo_img_class' => 'brand-image-xl',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'ORD Logo',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-classes
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'dashboard',

    'logout_url' => 'logout',

    'login_url' => 'login',

    'register_url' => 'register',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-menu
    |
    */

    'menu1' => [
//        [
//            'text' => 'search',
//            'search' => false,
//            'topnav' => false,
//        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
        ],

//        [
//            'text' => 'Dashboard',
//            'url'  => 'dashboard',
//            'icon' => 'fas fa-fw fa-tachometer-alt',
//        ],
        [
            'text' => 'Dashboard',
            'url'  => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
//        ['header' => 'shortcut_create'],

        [
            'text' => 'Create Request',
            'url'  => '#',
            'icon' => 'fas fa-fw fa-paper-plane',
            'submenu' => [
                [
                    'text' => 'Memo',
                    'url'  => 'request_memo/create',
                    'icon' => 'fas fa-plus',
                ],
                [
                    'text' => 'Special Expense',
                    'url'  => 'request/create',
                    'icon' => 'fas fa-plus',
                    'class' => 'create_request'
                ],
                [
                    'text' => 'General Expense',
                    'url'  => 'request_hr/create',
                    'icon' => 'fas fa-plus',
                    'class' => 'create_request_hr'
                ],
//                [
//                    'text' => 'Dispose',
//                    'url'  => 'request_dispose/create',
//                    'icon' => 'fas fa-plus',
//                ],
                [
                    'text' => 'Disposal',
                    'url'  => 'disposal/create',
                    'icon' => 'fas fa-plus',
                ],
            ]
        ],
        [
            'text' => 'Report',
            'url'  => 'dashboard',
            'icon' => 'fas fa-fw fa-chart-area',
            'submenu' => [
                [
                    'text' => 'Memo',
                    'url'  => 'request_memo',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Special Expense',
                    'url'  => 'request',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'General Expense',
                    'url'  => 'request_hr',
                    'icon' => 'fas fa-fw fa-list',
                ],
//                [
//                    'text' => 'Dispose',
//                    'url'  => 'request_dispose',
//                    'icon' => 'fas fa-fw fa-list',
//                ],
                [
                    'text' => 'Disposal',
                    'url'  => 'disposal',
                    'icon' => 'fas fa-fw fa-list',
                ],
            ]
        ],
//        ['header' => 'shortcut_create'],
        [
            'text' => 'Setting',
            'url'  => '#',
            'icon' => 'fas fa-fw fa-sliders-h',
            'submenu' => [
                [
                    'text' => 'Staff',
                    'url'  => 'user',
                    'icon' => 'fas fa-fw fa-user',
                ],
                [
                    'text' => 'Company',
                    'url'  => 'company',
                    'icon' => 'fas fa-building',
                ],
                [
                    'text' => 'Position',
                    'url'  => 'position',
                    'icon' => 'fas fa-fw fa-crosshairs',
                ],
                [
                    'text' => 'Branch',
                    'url'  => 'branch',
                    'icon' => 'fas fa-code-branch',
                ],
                [
                    'text' => 'Department',
                    'url'  => 'department',
                    'icon' => 'fas fa-building',
                ],
            ]
        ],



//        [
//            'text'        => 'pages',
//            'url'         => 'admin/pages',
//            'icon'        => 'far fa-fw fa-file',
//            'label'       => 4,
//            'label_color' => 'success',
//        ],
//        ['header' => 'account_settings'],
//        [
//            'text' => 'profile',
//            'url'  => 'admin/settings',
//            'icon' => 'fas fa-fw fa-user',
//        ],
//        [
//            'text' => 'change_password',
//            'url'  => 'admin/settings',
//            'icon' => 'fas fa-fw fa-lock',
//        ],
//        [
//            'text'    => 'multilevel',
//            'icon'    => 'fas fa-fw fa-share',
//            'submenu' => [
//                [
//                    'text' => 'level_one',
//                    'url'  => '#',
//                ],
//                [
//                    'text'    => 'level_one',
//                    'url'     => '#',
//                    'submenu' => [
//                        [
//                            'text' => 'level_two',
//                            'url'  => '#',
//                        ],
//                        [
//                            'text'    => 'level_two',
//                            'url'     => '#',
//                            'submenu' => [
//                                [
//                                    'text' => 'level_three',
//                                    'url'  => '#',
//                                ],
//                                [
//                                    'text' => 'level_three',
//                                    'url'  => '#',
//                                ],
//                            ],
//                        ],
//                    ],
//                ],
//                [
//                    'text' => 'level_one',
//                    'url'  => '#',
//                ],
//            ],
//        ],
//        ['header' => 'labels'],
//        [
//            'text'       => 'important',
//            'icon_color' => 'red',
//        ],
//        [
//            'text'       => 'warning',
//            'icon_color' => 'yellow',
//        ],
//        [
//            'text'       => 'information',
//            'icon_color' => 'aqua',
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],

        // loading lage 
        
        // [
        //     'name' => 'Pace',
        //     'active' => false,
        //     'files' => [
        //         [
        //             'type' => 'css',
        //             'asset' => false,
        //             'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
        //         ],
        //         [
        //             'type' => 'js',
        //             'asset' => false,
        //             'location' => 'https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
        //         ],
        //     ],
        // ],
    ],
];
