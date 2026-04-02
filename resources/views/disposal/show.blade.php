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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body, span, p, td {
            font-family: 'Times New Roman','Khmer OS Content';
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
            margin-bottom: 0.7rem;
        }
        .reviewer_section > div > img {
            height: 40px;
        }
        .reviewer_section {
            margin-bottom: 20px;
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
                bottom: 0;
            }
            div.action_btn, div#action {
                display: none;
            }
            .finance_section input {
                border: 0px;
            }
            .table-bordered td, .table-bordered th {
                border: 1px solid #1D1D1D !important;
            }
            @page
            {
                size: A4 landscape;
                margin: 1cm;
                width: 29.7cm;
                height: 21cm;
            }
        }
       /* @page {

            size: auto;
        }*/
        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 10px;
            text-align: center;
        }
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
        {{--@if(can_disposal($data))--}}
        @if(!can_approve_reject($data, config('app.type_disposal')))
            <button id="approve_btn" disabled class="btn btn-sm btn-success">
                Approve
            </button>
            <button id="reject_btn"  disabled  value="1" class="btn btn-sm btn-danger">
                Comment
            </button>
        @else
            <form action="{{ route('disposal.approve', $data->id) }}" method="POST" id="approve_memo_form" style="margin: 0; display: inline-block">
                @csrf
                <div class="btn-group" role="group" aria-label="">
                    <button formaction="approve_memo_form" id="approve_btn" name="approve" value="1" class="btn btn-sm btn-success">
                        Approve
                    </button>
                    <button formaction="reject_memo_form" id="reject_btn"  name="reject" value="1" class="btn btn-sm btn-danger">
                        Comment
                    </button>
                </div>
            </form>
            <form action="{{ route('disposal.reject', $data->id) }}" method="POST" id="reject_memo_form" style="margin: 0; display: inline-block">
                @csrf
            </form>
        @endif
    </div><br><br>

    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->push($data->verify())->push($data->approver())
    ])

{{--    <form style="margin: 0; display: inline-block " action="">--}}
{{--        <div class="btn-group" role="group" aria-label="">--}}
{{--            @if(--}}
{{--    $data->created_by == \Illuminate\Support\Facades\Auth::id() ||--}}
{{--    $data->status == config('app.approve_status_approve') ||--}}
{{--    $data->status == config('app.approve_status_reject')--}}
{{--    )--}}
{{--                <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">--}}
{{--                    Approve--}}
{{--                </button>--}}
{{--                <button id="reject_btn" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">--}}
{{--                    Reject--}}
{{--                </button>--}}
{{--            @else--}}
{{--                <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">--}}
{{--                    Approve--}}
{{--                </button>--}}
{{--                <button id="reject_btn" style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">--}}
{{--                    Reject--}}
{{--                </button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </form>--}}

</div>
<div class="a4 container-fluid" style="background: #FFF">
    <div class="row logo text-left" style="padding-top: 15px">
        <div class="col-sm-12 text-center">
            <img src="{{ asset($data->forcompany->logo) }}" alt="logo" style="height: 56px">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div>
                <br>
                <h1>សូមគោរពជូន</h1>
                @if(@$data->approver()->position_level == config('app.position_level_president'))
                    <h1>លោកស្រី{{@$data->forcompany->approver}}</h1>
                @else
                    <h1>លោក{{@$data->approver()->position->name_km}}</h1>
                @endif
            </div>
            <form id="hr_form" method="POST" action="">
                @csrf
                @method('post')
                <table  class="table table-borderless mb-0">
                    <tr>
                        <td style="width: 50px; vertical-align: top">
                            តាមរយ:
                        </td>
                        <td class="text-left">
                            @foreach($data->reviewers() as $reviewer)
                                <p class="mb-0">{{ $reviewer->position_name }}</p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td>កម្មវត្ថុៈ</td>
                        <td>សំណើសុំលុប (Disposal) សំភារៈ ដែលខូច ចេញពីបញ្ជីទ្រព្យសម្បត្តិ ដែលសំភារៈទាំងអស់នោះមានដូចខាងក្រោម៖</td>
                    </tr>
                </table>
                <table class="table table-bordered">
{{--                    <tr>--}}
{{--                        <td colspan="7"><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></td>--}}
{{--                        <td colspan="2" class="text-bold text-center">សម្រាប់មន្រ្តីហិរញ្ញវត្ថុ</td>--}}
{{--                        <td rowspan="17">--}}
{{--                            @include('request_hr.approve_section')--}}
{{--                        </td>--}}
{{--                    </tr>--}}
                    <tr class="text-center">
                        <td style="min-width: 30px;">ល.រ</td>
                        <td style="min-width: 120px">ឈ្មោះក្រុមហ៊ុន/សាខា
                        <td style="min-width: 130px">ឈ្មោះសំភារៈ</td>
                        <td>ប្រភេទសំភារៈ</td>
                        <td>កូដ</td>
                        <td style="min-width: 70px">ម៉ាក</td>
                        <td style="min-width: 70px">កាលបរិច្ឆេទទិញ</td>
                        <td style="min-width: 84px">កាលបរិច្ឆេទខូច</td>
                        <td style="min-width: 40px">ចំនួន</td>
                        <td style="min-width: 71px">មូលហេតុខូច</td>
                    </tr>
                    <?php $i = 1; ?>
                    @foreach($data->items as $key => $item)
                        <tr class="finance_section">
                            <td class="text-center">{{ $i++ }}</td>
                            <td class="text-center">{{ $item->company_name }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-center">{{ $item->asset_tye }}</td>
                            <td>{{ $item->code }}</td>
                            <td class="text-center">{{ $item->model }}</td>
                            <td class="text-center">
                                @if( $item->purchase_date != null)
                                    {{ $item->purchase_date->format('d-m-Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-center">{{ $item->broken_date->format('d-m-Y') }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td>{{ $item->desc }}</td>
                        </tr>
                    @endforeach

                    @for($i = 0; (5 - count($data->items)) > $i; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
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
                </table>
                <p class="mb-1"><strong>មូលហេតុៈ ដោយសារសំភារៈដែលបានរៀបរាប់ជូនខាងលើ ត្រូវបានខូច ហើយមិនអាចជួសជុលបាន ។</strong></p>
                <p class="mb-1">
                    អាស្រ័យ ដូចបានជម្រាបជូនខាងលើ
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        សូមលោកស្រី{{@$data->forcompany->approver}}
                    @else
                        សូមលោក{{@$data->approver()->position->name_km}}
                    @endif
                    មេត្តាពិនិត្យ និងអនុញ្ញាតដោយសេចក្តីអនុគ្រោះ ។
                </p>
                <p class="mb-1">
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        សូមលោកស្រី{{@$data->forcompany->approver}}
                    @else
                        សូមលោក{{@$data->approver()->position->name_km}}
                    @endif
                    មេត្តាទទួលនូវសេចក្តីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{prifixGender($data->requester()->gender)}}។
                </p>
            </form>
        </div>

    </div>
    <div class="row">
        <div class="col">
            <p>&nbsp;</p>
            <div class="text-center">
                <p>
                    ថ្ងៃទី​{{ khmer_number($data->created_at->format('d')) }} 
                    ខែ{{ khmer_month($data->created_at->format('m')) }}
                    ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}
                </p>
                <p>រៀបចំដោយ</p>
                <p>{{ $data->requester()->position->name_km }}</p>
                <p><img style="height: 60px" src="{{ asset('/'.$data->requester()->signature) }}" alt="Signature"></p>
                <p class="requester-name">{{ $data->requester()->name }}</p>
            </div>
        </div>
        @foreach($data->reviewers() as $reviewer)
            <div class="col">
                <p>&nbsp;</p>
                <div class="text-center">
                    @if($reviewer->approve_status== config('app.approve_status_approve'))
                        <p>
                            ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('d')) }}
                            ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('m')) }}
                            ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime($reviewer->approved_at))->format('Y')) }}
                        </p>
                        <p>បានត្រួតពិនិត្យដោយ</p>
                        <p>{{ $reviewer->position->name_km }}</p>
                        <p><img style="height: 60px" src="{{ asset('/'.$reviewer->signature) }}" alt="Signature"></p>
                        <p class="requester-name">{{ $reviewer->name }}</p>
                    @else
                        <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                        <p>បានត្រួតពិនិត្យដោយ</p>
                    @endif

                </div>
            </div>
        @endforeach
        <div class="col">
            <div class="text-center">
                <!-- <p>ធ្វើនៅការិយាល័យកណ្តាល, ថ្ងៃទី​{{ khmer_number($data->created_at->format('d')) }} ខែ {{ khmer_month($data->created_at->format('m')) }}  ឆ្នាំ{{ khmer_number($data->created_at->format('Y')) }}</p> -->
                <p>&nbsp;</p>
                @if(@$data->approver()->approve_status == config('app.approve_status_approve'))
                    <p>
                        ថ្ងៃទី{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('d')) }}
                        ខែ{{ khmer_month(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('m')) }}
                        ឆ្នំា{{ khmer_number(\Carbon\Carbon::createFromTimestamp(strtotime(@$data->approver()->approved_at))->format('Y')) }}
                    </p>
                    <p>អនុម័តដោយ</p>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->approver }}</p>
                    @else
                        <p>{{ @$data->approver()->position->name_km }}</p>
                    @endif
                    <p><img style="height: 60px" src="{{ asset('/'.@$data->approver()->signature) }}" alt="Signature"></p>
                    <p class="requester-name">{{ @$data->approver()->name }}</p>
                @else
                    <p>ថ្ងៃទី.....ខែ.....ឆ្នំា.....</p>
                    <p>អនុម័តដោយ</p>
                    @if(@$data->approver()->position_level == config('app.position_level_president'))
                        <p>{{ @$data->forcompany->approver }}</p>
                    @else
                        <p>{{ @$data->approver()->position->name_km }}</p>
                    @endif
                @endif

            </div>
        </div>
    </div>
    <div class="row footer">
        <img src="{{ asset($data->forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; height: 50px;">
    </div>
{{--    <p class="break"></p>--}}
    @include('global.comment_modal', ['route' =>route('disposal.reject', $data->id)])

</div>
<br>
<br>

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
            // window.localStorage.removeItem('previous');
        }
        else{
            alert("Can't previous");
        }
    });

    $( "#approve_btn" ).on( "click", function( event ) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                // $('#hr_form').submit();
                $.ajax({
                    type: "POST",
                    url: "{{ action('DisposalController@approve', $data->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        request_id: "{{ $data->id }}"
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.status) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'The request has been approved',
                                icon: 'success',
                                timer: '2000',
                            })
                            location.reload();
                        }
                        console.log(data.request_token)
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
        // Swal.fire({
        //     title: 'Please put the reason',
        //     // text: "You won't be able to revert this!",
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Yes',
        //     input: 'text',
        //     showLoaderOnConfirm: true,
        //     inputValidator: (value) => {
        //         if (!value) {
        //             return 'Comment is required!'
        //         }
        //     },
        //     allowOutsideClick: () => !Swal.isLoading()
        // }).then((result) => {

        //     if (result.value) {
        //         $.ajax({
        //             type: "POST",
        //             url: "{{ action('DisposalController@reject', $data->id) }}",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 request_id: "{{ $data->id }}",
        //                 comment: result.value,
        //             },
        //             dataType: "json",
        //             success: function(data) {
        //                 if (data.status) {
        //                     Swal.fire({
        //                         title: 'Reject!',
        //                         text: 'The request has been reject',
        //                         icon: 'success',
        //                         timer: '2000',
        //                     })
        //                     location.reload();
        //                 }
        //                 console.log(data.request_token)
        //             },
        //             error: function(data) {
        //                 console.log(data)
        //             }
        //         });
        //     }

        // })
    });
</script>
</html>
