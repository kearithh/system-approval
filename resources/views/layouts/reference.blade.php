<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 3 | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

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

    </style>

    @stack('css')
</head>
<?php $companyShortName = ['STSK', 'MFI', 'NGO', 'ORD', 'ST', 'MMI', 'MHT', 'TSP'] ?>
<body class="hold-transition sidebar-mini @if(request()->segment(1) == 'group_request' && in_array(request()->segment(2), $companyShortName) && isset($_GET['department'])) sidebar-collapse @endif">
<div class="wrapper">

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
{{--                    <div class="col-sm-6">--}}
{{--                        <ol class="breadcrumb">--}}
{{--                            <li class="breadcrumb-item"><a href="#">Home</a></li>--}}
{{--                            <li class="breadcrumb-item active">Dashboard v1</li>--}}
{{--                        </ol>--}}
{{--                    </div><!-- /.col -->--}}
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
    {{--        <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>--}}
    {{--        All rights reserved.--}}
    {{--        <div class="float-right d-none d-sm-inline-block">--}}
    {{--            <b>Version</b> 3.0.5--}}
    {{--        </div>--}}
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
<scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script>
    $('.tooltipsign').tooltip();

    $(document).ready(function() {
        $('.select2').select2();

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });
    });

</script>
@stack('js')
</body>
</html>
