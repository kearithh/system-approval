<!DOCTYPE html>
<html>

<head>
    <title>STSK E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    {{--<link href="/bootstrap3-wysihtml5.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body, span, p, td {
            font-family: 'Times New Roman', 'Khmer OS Content';
            font-weight: 400;
            font-size: 11px;
        }
        div.a4 {
            width: 29.7cm;
            /*height: 20cm;*/
            margin: auto;
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: .25rem;
            padding-right: .25rem;
        }
        .reviewer_section p {
            margin-bottom: 0.5rem;
        }
        .reviewer_section > div > img {
            height: 25px;
        }
        .reviewer_section {
            margin-bottom: 0px;
        }
        .footer {
            position: absolute;
            /*padding-top: 24px;*/
        }

        @media print {
            p.break {
                page-break-before: always;
            }
            .footer {
                position: fixed;
                /*padding-top: 24px;*/
                bottom: 0;
            }
            div.action_btn, div#action {
                display: none;
            }
            .finance_section input {
                border: 0px;
            }
            #approve_section {
                width: 175px !important;
            }
            .table-bordered td, .table-bordered th {
                border: 1px solid #1D1D1D !important;
            }

            @page
            {
                size: A4 landscape;
                margin: 1cm;
                /*width: 29.7cm;
                height: 21cm;*/
            }
        }

        /*@page {

            size: auto;
        }*/
    </style>

    <style>
        #action {
            position: relative;
            background: #FFF;
            z-index: 1;
            margin: auto;
            width: 29.7cm;
            margin-bottom: 5px;
        }

        #action_group{
            position: fixed;
            background: white;
        }

        #after_action {

            margin-top: 40px;
        }
    </style>
</head>

<body style="background: #dadada">

<div id="action">
    <div id="action_group">
        <div class="btn-group" role="group" aria-label="">
            <button id="back" type="button" class="btn btn-sm btn-secondary">
               Back
            </button>
        </div>
        @include('global.next_pre')

        <div class="btn-group" role="group" aria-label="">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
                Print
            </button>
        </div>
        <form style="margin: 0; display: inline-block " action="">
            <div class="btn-group" role="group" aria-label="">
    {{--            @if(--}}
    {{--    $data->created_by == \Illuminate\Support\Facades\Auth::id()--}}
    {{--    || $data->status == config('app.approve_status_approve')--}}
    {{--    || $data->status == config('app.approve_status_reject'))--}}
                @if(!can_approve_reject($data, config('app.type_general_expense')))

                    <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                        Approve
                    </button>
                    <button id="comment_modal" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                        Comment
                    </button>
                @else
                    <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                        Approve
                    </button>
                    <button id="reject_btn" style="background: #bd2130; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default">
                        Comment
                    </button>
                @endif
            </div>
        </form>
    </div><br><br>

    @include('global.rerviewer_table', ['reviewers' => [
        $agreeBy = $data->reviewers()->where('position', 'agree_by')->first(),
        $agreeByShort = $data->reviewers()->where('position', 'agree_by_short')->first(),
        $reviewer = $data->reviewers()->where('position', 'reviewer')->first(),
        $reviewerShort1 = $data->reviewers()->where('position', 'reviewer_short_1')->first(),
        $reviewerShort2 = $data->reviewers()->where('position', 'reviewer_short_2')->first(),
        $verify = $data->reviewers()->where('position', 'verify')->first(),
        $data->approver()
    ]])
</div>
<div class="a4 container-fluid" style="background: #FFF">
    <div class="row logo text-left" style="padding-top: 15px">
        <div class="col-sm-12">
            <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 56px">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <br>
            <form id="hr_form" method="POST" action="{{ action('RequestHRController@approve', $data->id) }}">
                @csrf
                @method('post')
                <table class="table table-bordered">
                <tr>
                    <td colspan="11">
                        <div class="float-left">
                            អ្នកស្នើសុំ | Request by: {{ $data->requester->name }}
                        </div>

                        <div class="float-right ">
                            <table class="table table-borderless  p-0 mb-0">
                                <tr>
                                    <td class="text-right p-0">តួនាទី|Position :&nbsp;</td>
                                    <td class="p-0"> {{ $data->requester->position->name_km }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right p-0">ទីតាំងការងារ|Location :&nbsp;</td>
                                    <td class="text-left p-0">{{ $data->requester->location }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td id="approve_section" class="text-center" style="min-width: 169px">
                        <span class="text-bold">សម្រាប់ផ្នែកអនុម័ត</span>
                        <br>
                        <span class="">For Approval section</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="9"><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></td>
                    <td colspan="2" class="text-bold text-center">សម្រាប់មន្រ្តីហិរញ្ញវត្ថុ</td>
                    <td rowspan="17" style="vertical-align: top">
                        @include('request_hr.approve_section')
                    </td>
                </tr>
                <tr class="text-center">
                    <td style="min-width: 30px;">ល.រ​<br> No.</td>
                    <td style="min-width: 200px">បរិយាយ <br> Description</td>
                    <td style="min-width: 200px">គោលបំណង <br> Purpose</td>
                    <td>បរិមាណ <br> QTY</td>
                    <td style="min-width: 40px">ឯកត្តា <br> Unit</td>
                    <td style="min-width: 72px">ថ្លៃឯកត្តា <br> Unit Price</td>
                    <td style="min-width: 72px">ទឹកប្រាក់ <br> Amount</td>
                    <td style="min-width: 55px">ថ្ងៃទិញចុងក្រោយ <br> Last Date of Purchasing</td>
                    <td style="min-width: 55px">ចំនួននៅសល់ <br> Remain QTY</td>
                    <td style="min-width: 84px">លេខគណនី <br> Account No.</td>
                    <td style="min-width: 71px">សមតុល្យ <br> Balance</td>
                </tr>
                <?php $i = 1; ?>
                @foreach($data->items as $key => $item)
{{--                    {{ dd($item) }}--}}
                    <tr class="finance_section">
                        <td class="text-center">{{ $i++ }}</td>
                        <td>{{ $item->desc }}</td>
                        <td>{{ $item->purpose }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-right">
                            @if($item->currency=='KHR')
                                {{ number_format($item->unit_price) .' ៛'}}
                            @else
                                {{'$ '. number_format(($item->unit_price),2) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->currency=='KHR')
                                {{ number_format($item->qty * $item->unit_price) .' ៛'}}
                            @else
                                {{'$ '. number_format(($item->qty * $item->unit_price),2) }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->last_purchase_date == null)
                                N/A
                            @else
                                {{\Carbon\Carbon::createFromTimestamp(strtotime($item->last_purchase_date))->format('d/m/Y') }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->remain_qty == null || $item->remain_qty == 0)
                                0
                            @else
                                {{ $item->remain_qty }}
                            @endif
                        </td>
                        <td>
                            <input
                                style="width: 75px;"
                                class="account_no"
                                type="text"
                                id="account_no"
                                name="account_no[{{$item->id}}]"
                                value="{{ $item->account_no }}"
                                readonly="true"
                            >
                        </td>
                        <td>
                            <input
                                style="width: 75px;"
                                class="balance"
                                type="text"
                                id="balance"
                                name="balance[{{$item->id}}]"
                                value="{{ $item->balance }}"
                                readonly="true"
                            >

                        </td>
                    </tr>
                @endforeach
                    @for($i = (count($data->items) - 9);  $i <= 0; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                <tr>
                    <td class="text-right" colspan="6">សរុប: </td>
                    <td colspan="5">
                        @if($data->total > 0 )
                            <strong>{{'$ '. number_format(($data->total),2) }}</strong> &emsp;&emsp;
                        @endif
                        @if($data->total_khr > 0 )
                            <strong>{{ number_format($data->total_khr) .' ៛'}}</strong>
                        @endif
                    </td>
                </tr>
            </table>
            </form>
        </div>
    </div>
    <div class="row footer">
        <img src="{{ asset($data->forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; height: 50px; background: white">
    </div>
    <p class="break"></p>
    @include('global.comment_modal', ['route' =>route('request_hr.reject', $data->id)])
</div>

@if(! config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery.inputmask.bundle.min.js') }}"></script>
    {{--<scrypt src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></scrypt>--}}
    @include('adminlte::plugins', ['type' => 'js'])
    <scrypt src="/bootstrap3-wysihtml5.min.js"></scrypt>

    @yield('adminlte_js')
@else
    {{--<script src="{{ asset('js/app.js') }}"></script>--}}
@endif
<script src="{{ asset('js/sweetalert2@9.js') }}"></script>

</body>
<script>
    {{--(function () {--}}
    {{--    var afterPrint = function () {--}}
    {{--        alert('Functionality to run after printing');--}}
    {{--        $.ajax({--}}
    {{--            type: "POST",--}}
    {{--            url: "{{ action('RequestHRController@approve', $data->id) }}",--}}
    {{--            data: {--}}
    {{--                _token: "{{ csrf_token() }}",--}}
    {{--            },--}}
    {{--            dataType: "json",--}}
    {{--            success: function(data) {--}}
    {{--                console.log(data)--}}
    {{--            },--}}
    {{--            error: function(data) {--}}
    {{--                console.log(data)--}}
    {{--            }--}}
    {{--        });--}}

    {{--    };--}}
    {{--    window.onafterprint = afterPrint;--}}
    {{--}());--}}
</script>
<script>

    $( "#back" ).on( "click", function( event ) {
        if(localStorage.previous){
            window.location.href = localStorage.previous;
            window.localStorage.removeItem('previous');
        }
        else{
            alert("Can't previous");
        }
    });

    $( "#approve_btn" ).on( "click", function( event ) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            // text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $('#hr_form').submit();
                $.ajax({
                    type: "POST",
                    url: "{{ action('RequestHRController@approve', $data->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        request_id: "{{ $data->id }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data)
                    },
                    error: function(data) {
                        console.log(data)
                    }
                });
            }
        })
    });
    $( "#reject_btn" ).on( "click", function( event ) {
        event.preventDefault();
        $('#comment_modal').modal('show');
    {{--Swal.fire({--}}
        {{--    title: 'Please put the reason',--}}
        {{--    // text: "You won't be able to revert this!",--}}
        {{--    icon: 'warning',--}}
        {{--    showCancelButton: true,--}}
        {{--    confirmButtonColor: '#3085d6',--}}
        {{--    cancelButtonColor: '#d33',--}}
        {{--    confirmButtonText: 'Yes',--}}
        {{--    input: 'text',--}}
        {{--    showLoaderOnConfirm: true,--}}
        {{--    inputValidator: (value) => {--}}
        {{--        if (!value) {--}}
        {{--            return 'Comment is required!'--}}
        {{--        }--}}
        {{--    },--}}
        {{--    allowOutsideClick: () => !Swal.isLoading()--}}
        {{--}).then((result) => {--}}

        {{--    if (result.value) {--}}
        {{--        $.ajax({--}}
        {{--            type: "POST",--}}
        {{--            url: "{{ action('RequestHRController@reject', $data->id) }}",--}}
        {{--            data: {--}}
        {{--                _token: "{{ csrf_token() }}",--}}
        {{--                request_id: "{{ $data->id }}",--}}
        {{--                comment: result.value,--}}
        {{--            },--}}
        {{--            dataType: "json",--}}
        {{--            success: function(data) {--}}
        {{--                if (data.status) {--}}
        {{--                    Swal.fire({--}}
        {{--                        title: 'Reject!',--}}
        {{--                        text: 'The request has been reject',--}}
        {{--                        icon: 'success',--}}
        {{--                        timer: '2000',--}}
        {{--                    })--}}
        {{--                    location.reload();--}}
        {{--                }--}}
        {{--                console.log(data.request_token)--}}
        {{--            },--}}
        {{--            error: function(data) {--}}
        {{--                console.log(data)--}}
        {{--            }--}}
        {{--        });--}}
        {{--    }--}}

        {{--})--}}
    });
</script>
@include('global.sweet_alert')
</html>
