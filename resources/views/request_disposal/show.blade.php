<!DOCTYPE html>
<html>

<head>
    <title>{{ $data->title_km }}</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Latest compiled and minified CSS -->
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>


    <!-- include summernote css/js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>

    <style>
        body {
            font-family: 'Khmer OS Content';
            font-weight: 400;
        }

        strong {
            font-family: 'Khmer OS Muol Light';
            font-size: 15px;
            font-weight: 400;
        }

        h1 {
            font-family: 'Khmer OS Muol Light';
            font-weight: 400;
        }

        .desc {
            text-align: center;
        }

        .signature {
            padding-top: 50px;
        }

        .signature > div {
            float: left;
            width: 33.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
        }
        .signature > div.col-2 {
            float: left;
            width: 49.33%;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box
        }
        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
        }

        table tr td, table tr th {
            border: 1px solid #585858;
            padding-top: 5px;
            padding-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            /*border-spacing: 0;*/
            width: 100%;
        }

        table thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        table h1 {
            margin-block-start: 17px;
        }

        h1 {
            font-size: 15px;
        }

        .desc_p p {
            /*margin: 0 !important;*/
            /*padding: 0 !important;*/
            /*margin-block-start: 0 !important;*/
            /*margin-block-end: 1em;*/
            font-size: 15px !important;

        }
        .desc_p {
            padding-left: 25px;
        }

        table tr td h1, table tr td p, table tr td span {
            margin: 0 !important;
            padding: 0 !important;
            margin-block-start: 0 !important;
            margin-block-end: 1em;
            font-size: 15px !important;

        }

        /*div.desc_p, div.desc_p span, div.desc_p p {*/
        /*padding-left: 25px;*/
        /*font-size: 16px !important;*/
        /*}*/

        div.action_btn {
            display: none;
            margin-top: 5px;
            position: fixed;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        div.action_btn a {
            padding: 10px 15px;
            text-decoration: none;
            color: inherit;
            /*border: 1px solid #dadada;*/
        }
        div.action_btn div {
            border-bottom: 1px solid #dadada;
        }
        p.requester-name {
            margin-bottom: 30px;
        }



        @media print {
            div.action_btn, div#action {
                display: none;
            }
        }

        .logo {
            text-align: center;
            padding-top: 70px;
            margin-bottom: 20px;
        }

        body div p, body div span, body div a {
            line-height: 1.7;
        }

        table tr td, table tr th {
            padding-left: 5px;
            padding-right: 5px;
        }


        pre#reason {
            display: block;
            padding: 0px;
            margin: 0 0 0px;
            font-size: inherit;
            line-height: inherit;
            color: inherit;
            word-break: break-all;
            word-wrap: break-word;
            background-color: inherit;
            border: 0px solid white !important;
            border-radius: 0px;
            overflow: unset;
        }
        pre#reason {
            font-family: 'Khmer OS Content', Monaco, Consolas, "Courier New", monospace;
        }
    </style>
    <style>
        #action {
            position: fixed;
            top: 0;
            margin-left: 1mm;
            background: white;
            z-index: 1;
            margin-top: 1mm;
        }
    </style>
</head>

<body style="background: #dadada">



<div style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
    {{--    <div class="action_contain">--}}
    {{--        <div class="action_btn">--}}
    {{--            <div>--}}
    {{--                <a href="" onclick="window.print()">Print</a>--}}
    {{--            </div>--}}
    {{--            <div>--}}
    {{--                <a href="/request_memo/{{$data->id}}/edit ">Edit</a>--}}
    {{--            </div>--}}
    {{--            <div>--}}
    {{--                <a href="">Save</a>--}}
    {{--            </div>--}}
    {{--            <div>--}}
    {{--                <a href="">Back</a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    <div id="action">
        <div class="btn-group" role="group" aria-label="">
            <a href="/request_dispose" type="button" class="btn btn-sm btn-default">
                Back
            </a>
        </div>
        <div class="btn-group" role="group" aria-label="">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-warning">
                Print
            </button>
        </div>
        <form style="margin: 0; display: inline-block " action="">
            <div class="btn-group" role="group" aria-label="">

                @if($data->created_by == \Illuminate\Support\Facades\Auth::id() || $data->status == config('app.approve_status_approve') || $data->status == config('app.approve_status_reject'))
                    <button id="approve_btn" disabled name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                        Approve
                    </button>
                    <button id="reject_btn" disabled style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                        Reject
                    </button>
                @else
                    <button id="approve_btn" name="previous" style="background: #28a745; color:white" value="1" class="btn btn-sm btn-default">
                        Approve
                    </button>
                    <button id="reject_btn" style="background: #bd2130; color: white" name="next" value="1" class="btn btn-sm btn-default">
                        Reject
                    </button>
                @endif
            </div>
        </form>

    </div>

    <div class="logo">
        <img src="{{ asset(asset($data->forcompany->logo) }}" alt="logo" style="width: 550px">
    </div>

    <div class="contain" style="padding: 0 110px">
        <div class="title" style="text-align: center; clear: both">
            <br>
            <h1 contenteditable="true">{!! trans('label.request_dispose_title') !!}</h1>
        </div>
        <div class="content" style="margin-top: 15px;">
            <div class="desc desc_p" style="text-align: left;">
                <p>
                    ខ្ញុំឈ្មោះ {{ $data->requester->name }} {{ $data->requester->gender ? 'ភេទ'.$data->requester->gender : ''}} តួនាទី{{ $data->requester->position->name }} បានខូចខាតនូវប្រភេទទ្រព្យសម្បត្តិដូចខាងក្រោម៖
                </p>

                <table>
                    <tr>
                        <th>លរ</th>
                        <th>ឈ្មោះទ្រព្យសម្បត្តិ</th>
                        <th>លេខកូដ</th>
                        <th>កាលបរិច្ឆេទទិញ</th>
                        <th>ចំនួន</th>
                        <th>កាលបរិច្ឆេទខូច</th>
                        <th>ទីកន្លែង</th>
                    </tr>
                    <?php $i = 1; ?>
                    @foreach($data->items as $key => $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->purchase_date->format('Y-m-d') }}</td>
                            <td>1</td>
                            <td>{{ $item->broken_date }}</td>
                            <td>{{ $item->location }}</td>
                        </tr>
                    @endforeach

                </table>
                <br>
                <p>
                    បរិយាយមូលហេតុ
                </p>
                <pre id="reason"><?php echo ($data->desc )?></pre>
                <br>
                <table>
                    <tr>
                        <th>កាត់ប្រាក់បុគ្គលិក</th>
                        <th>មិនកាត់ប្រាក់បុគ្គលិក</th>
                    </tr>
                    <tr>
                        <td>
                            <p>ឈ្មោះៈ {{ $penalty->name ? $penalty->name : 'N/A' }}</p>
                            <p>ទឹកប្រាក់ៈ {{ $penalty->amount ? $penalty->amount : 'N/A' }}</p>
                        </td>
                        <td>
                            <p>មូលហេតុៈ {{ $penalty->reason ? $penalty->reason : 'N/A' }}</p>
                        </td>
                    </tr>
                </table>

            </div>
            @include('request_disposal.partials.approve_section')
        </div>
    </div>
    <p style="clear: both"></p>
</div>
<div style="width: 1024px; margin: auto; margin-top: -66.06px">
    <div class="footer" style="background: orange; clear: both;">
        <img src="{{ asset($data->requester->company->footer) }}" alt="footer" style="width: 1024px; margin-bottom: -10px">
    </div>
</div>


</body>
<script>

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
                $.ajax({
                    type: "POST",
                    url: "{{ action('RequestDisposeController@approve', $data->id) }}",
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
                            });
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
        Swal.fire({
            title: 'Please put the reason',
            // text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            input: 'text',
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (!value) {
                    return 'Comment is required!'
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {

            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "{{ action('RequestDisposeController@reject', $data->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        request_id: "{{ $data->id }}",
                        comment: result.value,
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.status) {
                            Swal.fire({
                                title: 'Reject!',
                                text: 'The request has been reject',
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
</script>
</html>
