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
            font-size: 12px;
        }
        div.a4 {
            width: 29.7cm;
            /*height: 20cm;*/
            margin: auto;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-size: 13px;
            margin: 7px;
        }

        p{
            /*line-height: 1;*/
            margin-block-start: 5px;
            margin-block-end: 5px;
        }

        table.table td, table.table th {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            vertical-align: middle;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        .reviewer_section p {
            margin-bottom: 0.5rem;
        }
        .reviewer_section > div > img {
            height: 25px;
        }
        .content{
            padding-left: 60px;
            padding-right: 60px;
        }
        .reviewer_section {
            margin-bottom: 0px;
        }
        .footer {
            position: absolute;
            /*padding-top: 24px;*/
        }

        /*fix col note auto*/
        .row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          flex-wrap: wrap;
        }
        .row > [class*='col-'] {
          /*display: flex;*/
          flex-direction: column;
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

            .bgcol{
                background: #333333 !important;
            }

            @page
            {
                size: A4 landscape;
                margin: 0 !important;
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
        #after_action {
            margin-top: 40px;
        }
        #action_button{
            padding: 10px 0 0 10px;
            position: fixed;
            background-color: white;
        }
    </style>
</head>

<body style="background: #dadada">

<div id="action">
    <div id="action_button">
        <div class="btn-group" role="group" aria-label="">
            <button id="back" type="button" class="btn btn-sm btn-secondary" title="Back to list">
                Back
            </button>
        </div>
        @include('global.next_pre')
        <div class="btn-group" role="group" aria-label="">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning" title="Print/Export PDF">
                Print
            </button>
        </div>
        <form style="margin: 0; display: inline-block " action="">
            <div class="btn-group" role="group" aria-label="">
                @if(!can_approve_reject($data, config('app.type_damaged_log')))
                    <button disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default" title="Approve request">
                        Approve
                    </button>
                    <button disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Comment to creator update">
                        Comment
                    </button>
                    <button disabled style="background: black; color: white" name="next" value="1" class="btn btn-sm btn-default" title="Reject request">
                        Reject
                    </button>
                @else
                    <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default" title="Approve request">
                        Approve
                    </button>
                    <button id="reject_btn" style="background: #bd2130; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default" title="Comment to creator update">
                        Comment
                    </button>
                    <button id="disable_btn" style="background: black; color: white" name="next" data-target="comment_attach" value="1" class="btn btn-sm btn-default" title="Reject request, creator can't update">
                        Reject
                    </button>
                @endif
            </div>
        </form>
    </div><br><br>
    @include('global.rerviewer_table', ['reviewers' =>
        $data->reviewers()->merge($data->reviewers_short())->push($data->verify())->push($data->approver())
    ])
</div>
<div class="a4 container-fluid" style="background: #FFF">
    <div class="row logo text-center" style="padding-top: 30px">
        <div class="col-sm-12">

            <?php 
                $sections = @$data->forcompany->letterhead;
                $created_at = strtotime($data->created_at);
                $yes = 0;
                $forcompany = null;

                if ($sections) {

                    foreach($sections as $key => $section) {
                        $start = strtotime(@$section->start_effective);
                        $end = strtotime(@$section->end_effective);

                        if ($start <= $created_at && $end >= $created_at) {
                            $forcompany = $section; //contant in json
                            $yes += 1;
                        }
                    }

                } else {

                    $forcompany = $data->forcompany;
                    $yes += 1;

                }  

                if ($yes == 0) { // not in json
                    $forcompany = $data->forcompany; 
                }
            ?>

            <img src="{{ asset($forcompany->logo) }}" alt="logo" style="height: 70px">

        </div>
    </div>
    <div>
        <div class="title text-center">
            <h1>កំណត់ហេតុ</h1>
            <h1>ស្តីពីការខូចខាត ឬបាត់បង់ទ្រព្យសម្បត្តិ</h1>
        </div>
        <div class="row content">
            <div class="col-sm-12">
                <p>
                    {{ prifixGender($data->requester->gender) }}ឈ្មោះ {{ @$data->creator_object->name ?: $data->requester->name }} 
                    @if ($data->requester->gender)
                        ភេទ{{ genderKhmer($data->requester->gender) }}
                    @endif
                    តួនាទី{{ @$data->creator_object->position_name ?: $data->requester->position->name_km }}
                    សូមរាយការណ៍នូវប្រភេទទ្រព្យសម្បត្តិខូចខាតដូចខាងក្រោម៖
                </p>
                <table class="table table-bordered text-center">
                    <thead class="table-info">
                        <tr class="bgcol">
                            <th>លរ</th>
                            <th>ឈ្មោះទ្រព្យសម្បត្តិ</th>
                            <th>ឈ្មោះអ្នកប្រើប្រាស់</th>
                            <th>លេខកូដ</th>
                            <th>ចំនួន</th>
                            <th>កាលបរិច្ឆេទទិញ</th>
                            <th>កាលបរិច្ឆេទខូច</th>
                            <th>ទីកន្លែង</th>
                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    @foreach($damagedItem as $key => $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td class="text-left">{{ $item->name }}</td>
                            <td class="text-left">{{ $item->staff }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->number }} {{ $item->unit }}</td>
                            <td>
                                @if($item->purchase_date != null)
                                    {{(\Carbon\Carbon::createFromTimestamp(strtotime($item->purchase_date))->format('d-m-Y'))}}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{(\Carbon\Carbon::createFromTimestamp(strtotime($item->broken_date))->format('d-m-Y'))}}
                            </td>
                            <td>{{ $item->location }}</td>
                        </tr>
                    @endforeach
                </table>
                <p><strong>បរិយាយមូលហេតុ</strong></p>
                <p><?php echo ($data->desc )?></p>
                <table class="table table-bordered">
                    <thead class="text-center table-warning">
                        <tr>
                            <th>កាត់ប្រាក់បុគ្គលិក</th>
                            <th>មិនកាត់ប្រាក់បុគ្គលិក</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
                            @if(@$data->is_penalty == 1)
                                <p>ឈ្មោះៈ {{ @$penalty->name }}</p>
                                @if(@$penalty->currency == 'KHR')
                                    <p>ទឹកប្រាក់ៈ {{ number_format(@$penalty->amount) .' ៛'}}</p>
                                @else
                                    <p>ទឹកប្រាក់ៈ {{'$ '. number_format((@$penalty->amount),2) }}</p>
                                @endif
                            @else
                                <p>ឈ្មោះៈ N/A</p>
                                <p>ទឹកប្រាក់ៈ N/A</p>
                            @endif
                        </td>
                        <td>
                            @if($data->is_penalty == 0)
                                <p>មូលហេតុៈ {{ @$penalty->reason }}</p>
                            @else
                                <p>មូលហេតុៈ N/A</p>
                            @endif
                        </td>
                    </tr>
                </table>

                <span style="float: right !important;">
                    @foreach($data->reviewers_short() as $key => $value)
                        @if ($value->approve_status == config('app.approve_status_approve'))
                            <img  src="{{ asset($value->short_signature) }}"  
                                  alt="short_sign" 
                                  title="{{ @$value->name }}" 
                                  style="width: 25px;">
                        @endif
                    @endforeach
                </span>
            </div>
            <div class="col-sm-12">
                <div class="row text-center">
                    @include('damagedLog.partials.approve_section')
                </div>
            </div>

        </div>
    </div>
    <div class="row footer">
        <img src="{{ asset($forcompany->footer_landscape) }}" alt="logo" style="width: 29.7cm; height: auto; background: white">
    </div>
    <p class="break"></p>

    @include('global.comment_modal', ['route' =>route('damagedlog.reject', $data->id)])
    @include('global.disable_modal', ['route_disable' => route('damagedlog.disable', $data->id)])

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
            // text: "You won't be able to revert this!",
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
                    url: "{{ action('DamagedLogController@approve', $data->id) }}",
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

    // comment
    $( "#reject_btn" ).on( "click", function( event ) {
        event.preventDefault();
        $('#comment_modal').modal('show');
    });

    // reject
    $( "#disable_btn" ).on( "click", function( event ) {
        event.preventDefault();
        $('#disable_modal').modal('show');
    });

</script>
</html>
