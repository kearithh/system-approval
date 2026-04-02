<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="img/ord.jpg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title_prefix', config('adminlte.title_prefix', ''))
    @yield('title', config('adminlte.title', 'AdminLTE 3'))
    @yield('title_postfix', config('adminlte.title_postfix', ''))</title>
    @if(! config('adminlte.enabled_laravel_mix'))
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    @include('adminlte::plugins', ['type' => 'css'])

    @yield('adminlte_css_pre')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    @yield('adminlte_css')

    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"> -->
    @else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <!-- <link href="https://fonts.googleapis.com/css?family=Khmer&display=swap" rel="stylesheet"> -->

    <style>
        @font-face {
            font-family: 'Times New Roman';
            src:
                url("{{ asset('font/times.ttf') }}") format("truetype"),
                url("{{ asset('font/timesbd.ttf') }}") format("truetype"),
                url("{{ asset('font/timesbi.ttf') }}") format("truetype"),
                url("{{ asset('font/timesi.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: 'Adinda Melia';
            src: url("{{ asset('font/Adinda_Melia.otf') }}") format("opentype");
        }

        @font-face {
            font-family: 'Khmer OS Content';
            src: url("{{ asset('font/KhmerOScontent.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: 'Khmer OS Muol Light';
            src: url("{{ asset('font/KhmerOSmuollight.ttf') }}") format("truetype");
        }

        body {
            font-family: 'Times New Roman', 'Khmer OS Content';
        }

        .sidebar-dark-primary
        .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary
        .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #fd7e14!important;
            color: #fff;
        }

        .page-item.active .page-link {
            z-index: 1;
            color: #fff;
            background-color: #fd7e14;
            border-color: #fd7e14;
        }
        .page-link {
            color: inherit;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link {
            padding-left: 40px;
        }
        /*.nav .nav-treeview {*/
        /*    display:  inline-block;*/
        /*}*/
        .hidden {
            display: none !important;
        }
        .login-page {
            height: 70vh;
        }
        span.select2-container {
            width: 100% !important;
        }

        .request-type div ul li a i {
            width: 20px;
        }

        [v-cloak] {
            display: none;
        }

    </style>

    {{--<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>--}}
    {{--<script>--}}
        {{--var OneSignal = window.OneSignal || [];--}}
        {{--OneSignal.push(function() {--}}
            {{--OneSignal.init({--}}
                {{--appId: "77f1823c-5dca-4f8f-b66f-aaa816b126ba",--}}
            {{--});--}}
        {{--});--}}
        {{--OneSignal.getUserId().then(function(userId) {--}}
            {{--console.log("OneSignal User ID:", userId);--}}
            {{--$.ajax({--}}
                {{--type: "POST",--}}
                {{--url: "{{ action('UserController@appPlayerIdAjax') }}",--}}
                {{--data: {--}}
                    {{--_token: "{{ csrf_token() }}",--}}
                    {{--player_id: userId,--}}
                {{--},--}}
                {{--dataType: "json",--}}
                {{--success: function(data) {--}}
                    {{--console.log(data.request_token)--}}
                {{--},--}}
                {{--error: function(data) {--}}
                    {{--console.log(data)--}}
                {{--}--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}


</head>
<body class="@yield('classes_body')" @yield('body_data')>

@yield('body')

@if(! config('adminlte.enabled_laravel_mix'))
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
{{--<scrypt src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></scrypt>--}}
@include('adminlte::plugins', ['type' => 'js'])
<scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>
<script>
    $('.tooltipsign').tooltip()
</script>

@yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif

</body>
</html>
