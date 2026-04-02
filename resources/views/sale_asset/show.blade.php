{{--<!DOCTYPE html>--}}
{{--<html>--}}

{{--<head>--}}
{{--    <title>STSK E-Approval</title>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />--}}

{{--    <title>@yield('title_prefix', config('adminlte.title_prefix', ''))--}}
{{--        @yield('title', config('adminlte.title', 'AdminLTE 3'))--}}
{{--        @yield('title_postfix', config('adminlte.title_postfix', ''))</title>--}}
{{--    @if(! config('adminlte.enabled_laravel_mix'))--}}
{{--        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">--}}
{{--        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">--}}

{{--        @include('adminlte::plugins', ['type' => 'css'])--}}

{{--        @yield('adminlte_css_pre')--}}

{{--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">--}}
{{--        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">--}}

{{--        @yield('adminlte_css')--}}
{{--    @else--}}
{{--        <link rel="stylesheet" href="{{ asset('css/app.css') }}">--}}
{{--    @endif--}}
{{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">--}}
{{--    --}}{{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="{{ asset('css/app.css') }}">--}}
{{--    <style>--}}
{{--        body, span, p, td {--}}
{{--            font-family: 'Times New Roman', 'Khmer OS Content';--}}
{{--            font-weight: 400;--}}
{{--            font-size: 12px;--}}
{{--        }--}}
{{--        div.a4 {--}}
{{--            width: 29.7cm;--}}
{{--            /*height: 20cm;*/--}}
{{--            margin: auto;--}}
{{--        }--}}

{{--        h1 {--}}
{{--            font-family: 'Khmer OS Muol Light';--}}
{{--            font-size: 12px;--}}
{{--            margin: 7px 0 7px 0 !important;--}}
{{--        }--}}

{{--        p, span, b {--}}
{{--            font-family: 'Khmer OS Content' !important;--}}
{{--            font-size: 12px !important;--}}
{{--            margin: 5px 0 7px 0 !important;--}}
{{--        }--}}

{{--        table.table td, table.table th {--}}
{{--            padding-top: 0.25rem;--}}
{{--            padding-bottom: 0.25rem;--}}
{{--            /*vertical-align: middle;*/--}}
{{--            padding-left: 0.25rem;--}}
{{--            padding-right: 0.25rem;--}}
{{--        }--}}
{{--        .reviewer_section p {--}}
{{--            margin-bottom: 0.5rem;--}}
{{--        }--}}
{{--        .reviewer_section > div > img {--}}
{{--            height: 25px;--}}
{{--        }--}}
{{--        .content{--}}
{{--            padding-left: 60px;--}}
{{--            padding-right: 60px;--}}
{{--        }--}}
{{--        .reviewer_section {--}}
{{--            margin-bottom: 0px;--}}
{{--        }--}}
{{--        .footer {--}}
{{--            position: absolute;--}}
{{--            /*padding-top: 24px;*/--}}
{{--        }--}}

{{--        @media print {--}}
{{--            p.break {--}}
{{--                page-break-before: always;--}}
{{--            }--}}
{{--            .footer {--}}
{{--                position: fixed;--}}
{{--                /*padding-top: 24px;*/--}}
{{--                bottom: 0;--}}
{{--            }--}}
{{--            div.action_btn, div#action {--}}
{{--                display: none;--}}
{{--            }--}}
{{--            .finance_section input {--}}
{{--                border: 0px;--}}
{{--            }--}}
{{--            #approve_section {--}}
{{--                width: 175px !important;--}}
{{--            }--}}
{{--            .table-bordered td, .table-bordered th {--}}
{{--                border: 1px solid #1D1D1D !important;--}}
{{--            }--}}

{{--            .bgcol{--}}
{{--                background: #333333 !important;--}}
{{--            }--}}

{{--            @page--}}
{{--            {--}}
{{--                size: A4 landscape;--}}
{{--               /* margin: 0 !important;*/--}}
{{--            }--}}
{{--        }--}}

{{--        /*@page {--}}

{{--            size: auto;--}}
{{--        }*/--}}
{{--    </style>--}}

{{--    <style>--}}
{{--        #action {--}}
{{--            position: relative;--}}
{{--            background: #FFF;--}}
{{--            z-index: 1;--}}
{{--            margin: auto;--}}
{{--            width: 29.7cm;--}}
{{--            margin-bottom: 5px;--}}
{{--        }--}}
{{--        #after_action {--}}
{{--            margin-top: 40px;--}}
{{--        }--}}
{{--        #action_button{--}}
{{--            padding: 10px 0 0 10px;--}}
{{--            position: fixed;--}}
{{--            background-color: white;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}

{{--<body style="background: #dadada">--}}

{{--<div id="action">--}}
{{--    <div id="action_button">--}}
{{--        <div class="btn-group" role="group" aria-label="">--}}
{{--            <button id="back" type="button" class="btn btn-sm btn-secondary">--}}
{{--                Back--}}
{{--            </button>--}}
{{--        </div>--}}
{{--        @include('global.next_pre')--}}

{{--        <div class="btn-group" role="group" aria-label="">--}}
{{--            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">--}}
{{--                Print--}}
{{--            </button>--}}
{{--        </div>--}}
{{--        <form style="margin: 0; display: inline-block " action="">--}}
{{--            <div class="btn-group" role="group" aria-label="">--}}
{{--                @if(!can_approve_reject($data, config('app.type_sale_asset')))--}}
{{--                    <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">--}}
{{--                        Approve--}}
{{--                    </button>--}}
{{--                    <button id="comment_modal" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">--}}
{{--                        Comment--}}
{{--                    </button>--}}
{{--                @else--}}
{{--                    <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">--}}
{{--                        Approve--}}
{{--                    </button>--}}
{{--                    <button id="reject_btn" style="background: #bd2130; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default">--}}
{{--                        Comment--}}
{{--                    </button>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div><br><br>--}}
{{--    @include('global.rerviewer_table', ['reviewers' =>--}}
{{--        $data->reviewers()->push($data->approver())--}}
{{--    ])--}}
{{--</div>--}}
{{--<div class="a4 container-fluid" style="background: #FFF">--}}
{{--    <div class="row logo text-center" style="padding-top: 30px">--}}
{{--        <div class="col-sm-12">--}}
{{--            <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 70px">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <div class="title text-center">--}}
{{--            <h1>សូមគោរពជូន</h1>--}}
{{--            @if(@$data->approver()->position_level == config('app.position_level_president'))--}}
{{--                <h1>លោកស្រី{{@$data->forcompany->approver}}</h1>--}}
{{--            @else--}}
{{--                <h1>លោក{{@$data->approver()->position->name_km}}</h1>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        <div class="row content">--}}
{{--            <div class="col-sm-12">--}}
{{--                <table  class="table table-borderless mb-0">--}}
{{--                    <tr>--}}
{{--                        <td style="width: 50px; vertical-align: top">--}}
{{--                            <h1>តាមរយ:</h1>--}}
{{--                        </td>--}}
{{--                        <td class="text-left" style="vertical-align: top">--}}
{{--                            @foreach($data->reviewers() as $reviewer)--}}
{{--                                <p class="mb-0">{{ $reviewer->position_name }}</p>--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>--}}
{{--                            <h1>កម្មវត្ថុៈ<h1>--}}
{{--                            </td>--}}
{{--                        <td>--}}
{{--                            <p><?php echo ($data->purpose )?></p>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                </table>--}}
{{--                <table class="table table-bordered text-center">--}}
{{--                    <thead class="table-info">--}}
{{--                        <tr class="text-center">--}}
{{--                            <td style="min-width: 30px;">ល.រ</td>--}}
{{--                            <td style="min-width: 130px">ឈ្មោះទ្រព្យសម្បត្តិ</td>--}}
{{--                            <td style="min-width: 130px">កូដទ្រព្យសម្បត្តិ</td>--}}
{{--                            <td style="min-width: 70px">តម្លៃឯកត្តា</td>--}}
{{--                            <td style="min-width: 40px">ចំនួន</td>--}}
{{--                            <td style="min-width: 40px">សរុប</td>--}}
{{--                            <td style="min-width: 84px">អ្នកទិញ</td>--}}
{{--                            <td style="min-width: 71px">ផ្សេងៗ</td>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        <?php $i = 1; ?>--}}
{{--                        @foreach($data->items as $key => $item)--}}
{{--                            <tr class="finance_section">--}}
{{--                                <td class="text-center">{{ $i++ }}</td>--}}
{{--                                <td class="text-left">{{ $item->name }}</td>--}}
{{--                                <td>{{ $item->code }}</td>--}}
{{--                                <td class="text-center">--}}
{{--                                    @if($item->currency=='KHR')--}}
{{--                                        {{ number_format($item->unit_price) .' ៛'}}--}}
{{--                                    @else--}}
{{--                                        {{'$ '. number_format(($item->unit_price),2) }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td class="text-center">--}}
{{--                                    {{ $item->qty }} {{ $item->unit }}--}}
{{--                                </td>--}}
{{--                                <td class="text-center">--}}
{{--                                    @if($item->currency=='KHR')--}}
{{--                                        {{ number_format(($item->qty)*($item->unit_price)) .' ៛'}}--}}
{{--                                    @else--}}
{{--                                        {{'$ '. number_format((($item->qty)*($item->unit_price)),2) }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td>{{ $item->customer }}</td>--}}
{{--                                <td>{{ $item->others }}</td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}

{{--                        @for($i = 0; (5 - count($data->items)) > $i; $i++)--}}
{{--                            <tr>--}}
{{--                                <td>&nbsp;</td>--}}
{{--                                <td>&nbsp;</td>--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                                <td></td>--}}
{{--                            </tr>--}}
{{--                        @endfor--}}

{{--                        <tr>--}}
{{--                            <td class="text-right" colspan="4">--}}
{{--                                <strong>សរុប: </strong>--}}
{{--                            </td>--}}
{{--                            <td><strong>{{$data->total_item}}</strong></td>--}}
{{--                            <td colspan="3">--}}
{{--                                @if($data->total_usd > 0 )--}}
{{--                                    <strong>{{'$ '. number_format(($data->total_usd),2) }}</strong> &emsp;&emsp;--}}
{{--                                @endif--}}
{{--                                @if($data->total_khr > 0 )--}}
{{--                                    <strong>{{ number_format($data->total_khr) .' ៛'}}</strong>--}}
{{--                                @endif--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--                <p class="mb-1">--}}
{{--                    អាស្រ័យ ដូចបានជម្រាបជូនខាងលើ--}}
{{--                    @if(@$data->approver()->position_level == config('app.position_level_president'))--}}
{{--                        សូមលោកស្រី{{@$data->forcompany->approver}}--}}
{{--                    @else--}}
{{--                        សូមលោក{{@$data->approver()->position->name_km}}--}}
{{--                    @endif--}}
{{--                    មេត្តាពិនិត្យ និងអនុញ្ញាតដោយសេចក្តីអនុគ្រោះ ។--}}
{{--                </p>--}}
{{--                <p class="mb-1">--}}
{{--                    @if(@$data->approver()->position_level == config('app.position_level_president'))--}}
{{--                        សូមលោកស្រី{{@$data->forcompany->approver}}--}}
{{--                    @else--}}
{{--                        សូមលោក{{@$data->approver()->position->name_km}}--}}
{{--                    @endif--}}
{{--                    មេត្តាទទួលនូវសេចក្តីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។--}}
{{--                </p>--}}
{{--            <div class="col-sm-12">--}}
{{--                @include('sale_asset.partials.approve_section')--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="row footer">--}}
{{--        <img src="{{ asset($data->forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; background: white">--}}
{{--    </div>--}}
{{--    <p class="break"></p>--}}
{{--    @include('global.comment_modal', ['route' =>route('sale_asset.reject', $data->id)])--}}
{{--</div>--}}

{{--@if(! config('adminlte.enabled_laravel_mix'))--}}
{{--    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>--}}
{{--    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>--}}
{{--    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>--}}
{{--    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>--}}
{{--    @include('adminlte::plugins', ['type' => 'js'])--}}
{{--    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>--}}

{{--    @yield('adminlte_js')--}}
{{--@else--}}
{{--    --}}{{--<script src="{{ asset('js/app.js') }}"></script>--}}
{{--@endif--}}
{{--<script src="{{ asset('js/sweetalert2@9.js') }}"></script>--}}

{{--</body>--}}

{{--<script>--}}

{{--    $( "#back" ).on( "click", function( event ) {--}}
{{--        if(localStorage.previous){--}}
{{--            window.location.href = localStorage.previous;--}}
{{--            window.localStorage.removeItem('previous');--}}
{{--        }--}}
{{--        else{--}}
{{--            alert("Can't previous");--}}
{{--        }--}}
{{--    });--}}

{{--    $( "#approve_btn" ).on( "click", function( event ) {--}}
{{--        event.preventDefault();--}}
{{--        Swal.fire({--}}
{{--            title: 'Are you sure?',--}}
{{--            // text: "You won't be able to revert this!",--}}
{{--            icon: 'warning',--}}
{{--            showCancelButton: true,--}}
{{--            confirmButtonColor: '#3085d6',--}}
{{--            cancelButtonColor: '#d33',--}}
{{--            confirmButtonText: 'Yes'--}}
{{--        }).then((result) => {--}}
{{--            if (result.value) {--}}
{{--                // $('#hr_form').submit();--}}
{{--                $.ajax({--}}
{{--                    type: "POST",--}}
{{--                    url: "{{ action('SaleAssetController@approve', $data->id) }}",--}}
{{--                    data: {--}}
{{--                        _token: "{{ csrf_token() }}",--}}
{{--                        request_id: "{{ $data->id }}"--}}
{{--                    },--}}
{{--                    dataType: "json",--}}
{{--                    success: function(data) {--}}
{{--                        if (data.status) {--}}
{{--                            Swal.fire({--}}
{{--                                title: 'Approved!',--}}
{{--                                text: 'The request has been approved',--}}
{{--                                icon: 'success',--}}
{{--                                timer: '2000',--}}
{{--                            })--}}
{{--                            location.reload();--}}
{{--                        }--}}
{{--                        console.log(data.request_token)--}}
{{--                    },--}}
{{--                    error: function(data) {--}}
{{--                        console.log(data)--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}
{{--        })--}}
{{--    });--}}
{{--    $( "#reject_btn" ).on( "click", function( event ) {--}}
{{--        event.preventDefault();--}}
{{--        $('#comment_modal').modal('show');--}}
{{--    });--}}
{{--</script>--}}
{{--</html>--}}
