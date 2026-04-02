@extends('adminlte::master')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

{{--@section('plugins.Sweetalert2', true)--}}
@section('plugins.Select2', true)


@section('classes_body',
    (config('adminlte.sidebar_mini', true) === true ?
        'sidebar-mini ' :
        (config('adminlte.sidebar_mini', true) == 'md' ?
         'sidebar-mini sidebar-mini-md ' : '')
    ) .
    (config('adminlte.layout_topnav') || View::getSection('layout_topnav') ? 'layout-top-nav ' : '') .
    (config('adminlte.layout_boxed') ? 'layout-boxed ' : '') .
    (!config('adminlte.layout_topnav') && !View::getSection('layout_topnav') ?
        (config('adminlte.layout_fixed_sidebar') ? 'layout-fixed ' : '') .
        (config('adminlte.layout_fixed_navbar') === true ?
            'layout-navbar-fixed ' :
            (isset(config('adminlte.layout_fixed_navbar')['xs']) ? (config('adminlte.layout_fixed_navbar')['xs'] == true ? 'layout-navbar-fixed ' : 'layout-navbar-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_navbar')['sm']) ? (config('adminlte.layout_fixed_navbar')['sm'] == true ? 'layout-sm-navbar-fixed ' : 'layout-sm-navbar-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_navbar')['md']) ? (config('adminlte.layout_fixed_navbar')['md'] == true ? 'layout-md-navbar-fixed ' : 'layout-md-navbar-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_navbar')['lg']) ? (config('adminlte.layout_fixed_navbar')['lg'] == true ? 'layout-lg-navbar-fixed ' : 'layout-lg-navbar-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_navbar')['xl']) ? (config('adminlte.layout_fixed_navbar')['xl'] == true ? 'layout-xl-navbar-fixed ' : 'layout-xl-navbar-not-fixed ') : '')
        ) .
        (config('adminlte.layout_fixed_footer') === true ?
            'layout-footer-fixed ' :
            (isset(config('adminlte.layout_fixed_footer')['xs']) ? (config('adminlte.layout_fixed_footer')['xs'] == true ? 'layout-footer-fixed ' : 'layout-footer-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_footer')['sm']) ? (config('adminlte.layout_fixed_footer')['sm'] == true ? 'layout-sm-footer-fixed ' : 'layout-sm-footer-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_footer')['md']) ? (config('adminlte.layout_fixed_footer')['md'] == true ? 'layout-md-footer-fixed ' : 'layout-md-footer-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_footer')['lg']) ? (config('adminlte.layout_fixed_footer')['lg'] == true ? 'layout-lg-footer-fixed ' : 'layout-lg-footer-not-fixed ') : '') .
            (isset(config('adminlte.layout_fixed_footer')['xl']) ? (config('adminlte.layout_fixed_footer')['xl'] == true ? 'layout-xl-footer-fixed ' : 'layout-xl-footer-not-fixed ') : '')
        )
        : ''
    ) .
    (config('adminlte.sidebar_collapse') || View::getSection('sidebar_collapse') ? 'sidebar-collapse ' : '') .
    (config('adminlte.right_sidebar') && config('adminlte.right_sidebar_push') ? 'control-sidebar-push ' : '') .
    config('adminlte.classes_body')
)

@section('body_data',
(config('adminlte.sidebar_scrollbar_theme', 'os-theme-light') != 'os-theme-light' ? 'data-scrollbar-theme=' . config('adminlte.sidebar_scrollbar_theme')  : '') . ' ' . (config('adminlte.sidebar_scrollbar_auto_hide', 'l') != 'l' ? 'data-scrollbar-auto-hide=' . config('adminlte.sidebar_scrollbar_auto_hide')   : ''))

@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'dashboard') )

@if (config('adminlte.use_route_url', false))
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('body')
    <div class="wrapper">
        @if(config('adminlte.layout_topnav') || View::getSection('layout_topnav'))
        <nav class="main-header navbar {{config('adminlte.classes_topnav_nav', 'navbar-expand-md')}} {{config('adminlte.topnav_color', 'navbar-white navbar-light')}}">
            <div class="{{config('adminlte.classes_topnav_container', 'container')}}">
                @if(config('adminlte.logo_img_xl'))
                    <a href="{{ $dashboard_url }}" class="navbar-brand logo-switch">
                        <img src="{{ asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="{{config('adminlte.logo_img_class', 'brand-image-xl')}} logo-xs">
                        <img src="{{ asset(config('adminlte.logo_img_xl')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="{{config('adminlte.logo_img_xl_class', 'brand-image-xs')}} logo-xl">
                    </a>
                @else
                    <a href="{{ $dashboard_url }}" class="navbar-brand {{ config('adminlte.classes_brand') }}">
                        <img src="{{ asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="brand-image img-circle elevation-3" style="opacity: .8">
                        <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
                            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                        </span>
                    </a>
                @endif

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="nav navbar-nav">
                        @each('adminlte::partials.menu-item-top-nav', $adminlte->menu(), 'item')
                    </ul>
                </div>
            @else
            <nav class="main-header navbar {{config('adminlte.classes_topnav_nav', 'navbar-expand-md')}} {{config('adminlte.classes_topnav', 'navbar-white navbar-light')}}">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" @if(config('adminlte.sidebar_collapse_remember')) data-enable-remember="true" @endif @if(!config('adminlte.sidebar_collapse_remember_no_transition')) data-no-transition-after-reload="false" @endif @if(config('adminlte.sidebar_collapse_auto_size')) data-auto-collapse-size="{{config('adminlte.sidebar_collapse_auto_size')}}" @endif>
                            <i class="fas fa-bars"></i>
                            <span class="sr-only">{{ __('adminlte::adminlte.toggle_navigation') }}</span>
                        </a>
                    </li>
                    @each('adminlte::partials.menu-item-top-nav', $adminlte->menu(), 'item')
                    @yield('content_top_nav_left')
                </ul>

            @endif
                <ul class="navbar-nav ml-auto @if(config('adminlte.layout_topnav') || View::getSection('layout_topnav'))order-1 order-md-3 navbar-no-expand @endif">
                    @yield('content_top_nav_right')
{{--                    <li class="nav-item dropdown">--}}
{{--                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">--}}
{{--                            <i class="far fa-bell" style="font-size: 23px"></i>--}}
{{--                            <span class="badge bg-orange navbar-badge" style="font-size: 14px; color: #fff !important;">--}}
{{--                                {{ $totalPendingRequest +  $totalPendingReview}}--}}
{{--                            </span>--}}
{{--                        </a>--}}

{{--                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">--}}
{{--                            --}}{{--<span class="dropdown-item dropdown-header">15 Notifications</span>--}}
{{--                            <div class="dropdown-divider"></div>--}}
{{--                            <a href="#" class="dropdown-item">--}}
{{--                                <i class="fas fa-envelope mr-2"></i> {{ $totalPendingRequest }} Your pending request--}}
{{--                                --}}{{--<span class="float-right text-muted text-sm">3 mins</span>--}}
{{--                            </a>--}}
{{--                            --}}{{--<div class="dropdown-divider"></div>--}}
{{--                            --}}{{--<a href="#" class="dropdown-item">--}}
{{--                                --}}{{--<i class="fas fa-users mr-2"></i> 8 friend requests--}}
{{--                                --}}{{--<span class="float-right text-muted text-sm">12 hours</span>--}}
{{--                            --}}{{--</a>--}}
{{--                            <div class="dropdown-divider"></div>--}}
{{--                            <a href="#" class="dropdown-item">--}}
{{--                                <i class="fas fa-file mr-2"></i> {{ $totalPendingReview }} The request pending on you--}}
{{--                                --}}{{--<span class="float-right text-muted text-sm">2 days</span>--}}
{{--                            </a>--}}
{{--                            <div class="dropdown-divider"></div>--}}
{{--                        </div>--}}
{{--                    </li>--}}
                    @if(Auth::user())
                        <!-- <li style="margin-top: 8px;">{{ Auth::user()->name }}</li> -->
                        <li class="nav-item">
                            <a class="nav-link" style="color: black" title="View Profile" href="{{ route('user.edit', Auth::id()) }}">
                                <i class="fas fa-user"></i>
                                {{ Auth::user()->name_en }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            >
                                <i class="fa fa-fw fa-power-off"></i> {{ __('adminlte::adminlte.log_out') }}
                            </a>
                            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                                @if(config('adminlte.logout_method'))
                                    {{ method_field(config('adminlte.logout_method')) }}
                                @endif
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endif
                    @if(config('adminlte.right_sidebar'))
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-widget="control-sidebar" @if(!config('adminlte.right_sidebar_slide')) data-controlsidebar-slide="false" @endif @if(config('adminlte.right_sidebar_scrollbar_theme', 'os-theme-light') != 'os-theme-light') data-scrollbar-theme="{{config('adminlte.right_sidebar_scrollbar_theme')}}" @endif @if(config('adminlte.right_sidebar_scrollbar_auto_hide', 'l') != 'l') data-scrollbar-auto-hide="{{config('adminlte.right_sidebar_scrollbar_auto_hide')}}" @endif>
                                <i class="{{config('adminlte.right_sidebar_icon')}}"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            @if(config('adminlte.layout_topnav') || View::getSection('layout_topnav'))
                    </nav>
                @endif
            </nav>
        @if(!config('adminlte.layout_topnav') && !View::getSection('layout_topnav'))
        <aside class="main-sidebar {{config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4')}}">
            @if(config('adminlte.logo_img_xl'))
                <a href="{{ $dashboard_url }}" class="brand-link logo-switch">
                    <img src="{{ asset(config('adminlte.logo_img_ord', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="{{config('adminlte.logo_img_class', 'brand-image-xl')}} logo-xs">
                    <img src="{{ asset(config('adminlte.logo_img_xl')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="{{config('adminlte.logo_img_xl_class', 'brand-image-xs')}} logo-xl">
                </a>
            @else
                <a href="{{ $dashboard_url }}" class="brand-link {{ config('adminlte.classes_brand') }}">
                    <img src="{{ asset(config('adminlte.logo_img_ord', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}" alt="{{config('adminlte.logo_img_alt', 'AdminLTE')}}" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
                        {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                    </span>
                </a>
            @endif
            <div class="sidebar">
                @include('layouts.menu')
{{--                <nav class="mt-2">--}}
{{--                    <ul class="nav nav-pills nav-sidebar flex-column {{config('adminlte.classes_sidebar_nav', '')}}" data-widget="treeview" role="menu" @if(config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{config('adminlte.sidebar_nav_animation_speed')}}" @endif @if(!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>--}}
{{--                        @each('adminlte::partials.menu-item', $adminlte->menu(), 'item')--}}
{{--                    </ul>--}}
{{--                </nav>--}}
            </div>
        </aside>
        @endif

        <div class="content-wrapper">
            @if(config('adminlte.layout_topnav') || View::getSection('layout_topnav'))
            <div class="container">
            @endif

            <div class="content-header">
                <div class="{{config('adminlte.classes_content_header', 'container-fluid')}}">
                    @yield('content_header')
                </div>
            </div>

            <div class="content" id="request_type">
                <div class="{{config('adminlte.classes_content', 'container-fluid')}}">
                    @yield('content')
                </div>
            </div>
            @if(config('adminlte.layout_topnav') || View::getSection('layout_topnav'))
            </div>
            @endif
        </div>

        @hasSection('footer')
        <footer class="main-footer">

            @yield('footer')
        </footer>
        @endif

        @if(config('adminlte.right_sidebar'))
            <aside class="control-sidebar control-sidebar-{{config('adminlte.right_sidebar_theme')}}">
                @yield('right-sidebar')
            </aside>
        @endif

    </div>
@stop

@section('adminlte_js')
    
    <!-- vue file -->
    <script src="{{ asset('vue/vue@2.6.11') }}"></script>
    <script src="{{ asset('vue/axios.min.js') }}"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    @stack('js')
    @yield('js')
    @include('global.sweet_alert')
    <script>

        /*-Auto logout when user no action 3 minutes-*/
        var idleTime = 0;
        $(document).ready(function () {

            var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

            // $(this).mousemove(function () {
            //     idleTime = 0;
            // });
            // $(this).keypress(function () {
            //     idleTime = 0;
            // });

            // check keypress keyup mouseover
            $(this).on('keypress keyup mouseover', function() {
                idleTime = 0;
            });

        });

         // timer auto logout 
        function timerIncrement() {
            idleTime = idleTime + 1;
            //console.log(idleTime);
            if (idleTime > 119) { // 120 minutes
                console.log("logout");
                document.getElementById('logout-form').submit();
            }
        }


        // let uri = window.location.search.substring(1);
        // let queryString = new URLSearchParams(uri);
        // console.log(queryString.get("company"));

        new Vue({
            el: '#main_menu',
            data: {
                company: '',
                type: '',

                total_pending: 0,
                total_to_approve: 0,
                total_to_approve_report: 0,
                total_rejected: 0,
                total_disabled: 0,
                total_approved: 0,
                to_approve_group_support: 0,

                pending:{},
                to_approve:{},
                to_approve_report:{},
                rejected:{},
                disabled:{},
                approved:{},
            },
            methods:{
                sum: function ( obj ) {
                    var sum = 0;
                    for( var el in obj ) {

                        if( obj.hasOwnProperty( el ) ) {
                            sum += parseFloat( obj[el] );
                        }
                    }
                    return sum;
                }
            },
            created: async function () {
                let uri = window.location.search.substring(1);
                let queryString = new URLSearchParams(uri);
                const url = "/get-number-notification?company="+queryString.get('company')+"&type="+queryString.get('type')+"&menu="+"{{ request()->segment(1) }}";
                const {data} = await axios.get(url);
                this.company = data.company;
                this.type = data.type;

                this.pending = data.pending;
                this.to_approve = data.to_approve;
                this.to_approve_report = data.report_menu;
                this.rejected = data.rejected;
                this.disabled = data.disabled;
                this.approved = data.approved;

                this.total_pending = this.sum(data.pending);
                this.total_to_approve = this.sum(data.to_approve);
                this.total_to_approve_report = this.sum(data.report_menu);
                this.total_rejected = this.sum(data.rejected);
                this.total_disabled = this.sum(data.disabled);
                this.total_approved = this.sum(data.approved);

                this.to_approve_group_support = data.to_approve_group_support;
                // // sum main manu
                // this.total_to_approve_report = this.total_to_approve_report + this.to_approve_group_support;
                // console.log(data);
            }
        });

        new Vue({
            el: '#request_type',
            data: {
                company: '',
                type: '',
                menuType: '',
                requestType:{},
                total: {},

                departmentShortName:'',
                company_departments: {},
                departmentLink: '',

                tags: [],

                departments: [],

                groups: [],
            },
            mounted () {
                // console.log("moun");
                // $('.datepicker').datepicker(); //like normal jquery usage
                $('.datepicker').datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight:true,
                    autoclose: true
                });
                $(".select2").select2();
            },
            created: async function () {
                // console.log("created");
                var currentUrl = window.location.pathname;
                let uri = window.location.search.substring(1);
                let queryString = new URLSearchParams(uri);

                // Param for Controller
                var menuType = currentUrl.replace("/", "");
                var company = queryString.get('company');
                var type = queryString.get('type');
                var department = queryString.get('department');
                var tags = queryString.get('tags');
                var groups = queryString.get('groups');


                // Get Request Type, Company Department, and Tags
                if(company) {
                    //menuType, company, type, department, tags
                    var url = "/get-number-request-type?";
                    url += "menu="+menuType;
                    url += "&company="+company;
                    url += "&type="+type;
                    url += "&department="+department;
                    url += "&tags="+tags;
                    url += "&groups="+groups;
                    var {data} = await axios.get(url);
                    this.requestType = data.requestType;
                    this.company_departments = data.company_departments;
                    this.tags = data.tags;
                    //this.groups = data.groups;

                    if(menuType && type){
                        //approved in show department
                        var url_depart = "/get-number-department-type?";
                        url_depart += "menu="+menuType;
                        url_depart += "&company="+company;
                        url_depart += "&type="+type;
                        url_depart += "&department="+department;
                        var {data} = await axios.get(url_depart);
                        this.departments = data.departments;
                        // console.log(data.departments);
                    }
                    else{
                        this.departments = null;
                    }

                    if((menuType == 'approved' || menuType == 'to_approve_report' || menuType == 'toapprove' || menuType == 'pending') && type){
                        //get group request
                        var url_group = "/get-number-group?";
                        url_group += "menu="+menuType;
                        url_group += "&company="+company;
                        url_group += "&type="+type;
                        url_group += "&tags="+tags;
                        url_group += "&groups="+groups;
                        url_group += "&department="+department;
                        var {data} = await axios.get(url_group);
                        this.groups = data.groups;
                        // console.log(data.groups);
                    }
                    else{
                        this.groups = null;
                    }

                    // this.departmentLink = window.location.href;

                    // this.departmentShortName = queryString.get('department');
                    // this.company = data.company;
                    // this.type = data.type;
                    // this.menuType = data.menu_type;

                    // var cdData = [];
                    // data.company_departments.map(function(value, key) {
                    //     var link = value.link+"/"+value.short_name;
                    //     cdData.push(value.link = link);
                    // });


                    // this.total = data.total;


                    // console.log(this.requestType);
                    // console.log(cdData);
                   // console.log(data);
                }
                else if(menuType == 'to_approve_group_support') {
                    //get group support
                    var url_gr_sup = "/get-number-group-support?";
                    url_gr_sup += "menu="+menuType;
                    url_gr_sup += "&department="+department;
                    url_gr_sup += "&tags="+tags;
                    url_gr_sup += "&groups="+groups;
                    var {data} = await axios.get(url_gr_sup);
                    this.groups = data.groups;
                    this.tags = data.tags;
                    this.company_departments = data.company_departments;
                    // console.log(data.groups);
                }
                else {
                    this.groups = null;
                }

            },
            methods: {
                hello() {
                    console.log("method");
                }
            }
        });


    </script>
@stop
