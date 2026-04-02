<!DOCTYPE html>
<html>

<head>
    <title>E-Approval</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Times New Roman', 'Khmer OS Content';
            font-weight: 400;
        }

        strong {
            font-family: 'Khmer OS Muol Light';
            font-size: 14px;
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
        .related {
            float: left;
            text-align: center;
            /*border: 1px solid;*/
            box-sizing: border-box;
            text-overflow: ellipsis;
        }

        table tr td {
            border: 1px solid #585858;
            padding: 5px 5px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        table thead tr {
            /*background: orange;*/
            font-weight: 700;
            text-align: center;
        }

        ul li {
            list-style: none;
            margin-left: -40px;
        }
        @media print {
            .footer img {
                position: absolute;
                bottom: 0;
                margin-bottom: 0 !important;
                width: 100%;
            }
        }
    </style>
</head>

<body style="background: #dadada">
<div style="width: 1024px; margin: auto; margin-top: -66.06px">
    <div class="footer" style="background: orange; clear: both;">
        <img src="{{ asset($data->forcompany->footer) }}" alt="footer" style="width: 1024px; margin-bottom: -10px">
    </div>
</div>


</body>
<script src="{{ asset('js/sweetalert2@9.js') }}"></script>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>

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
                $('#hr_form').submit();
                $.ajax({
                    type: "POST",
                    url: "{{ action('RequestFormController@approve', $data->id) }}",
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
                            setTimeout(function(){
                                location.reload();
                            }, 2000);
                        }
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
                    url: "{{ action('RequestFormController@reject', $data->id) }}",
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
                            setTimeout(function(){
                                location.reload();
                            }, 2000);
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
