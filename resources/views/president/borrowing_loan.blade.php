@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        .table td, .table th {
            padding: .75rem .3rem;
        }
    </style>
@stop

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                  @include('president.partials.navigation')
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-lg-2">
                    @include('president.partials.nav_type')
                </div>
                <div class="col-md-9 col-lg-10">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title "><strong>Borrowing Loan List</strong></h4>
                        </div>
                        <div class="table-responsive" style="padding: 0 10px">
                            <table class="table table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th style="min-width: 50px;">ល.រ</th>
                                        <th style="min-width: 100px;">សកម្មភាព</th>
                                        <th style="min-width: 100px;">ស្ថានភាព</th>
                                        <th style="min-width: 220px;">ឈ្មោះសាខា</th>
                                        <th style="min-width: 220px;">ឈ្មោះអតិថិជន</th>
                                        <th style="min-width: 150px;">ទឺកប្រាក់</th>
                                        <th style="min-width: 170px;">ការប្រាក់ប្រចាំឆ្នាំ(%)</th>
                                        <th style="min-width: 200px;">ស្នើរដោយ</th>
                                        <th style="min-width: 215px;">ពិនិត្យដោយ</th>
                                        <th style="min-width: 245px;">អនុម័តដោយ</th>
                                        <th style="min-width: 200px;">កាលបរិច្ឆេទស្នើរ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data->count())
                                        <?php $i = 1; ?>
                                        @foreach($data as $key => $item)
                                            <tr>
                                                <td> {{ $i++  }}</td>
                                                <td class="td-actions">
                                                    @include('global.list_action', ['uri' => 'borrowing_loan', 'object' => $item])
                                                </td>
                                                <td>{{ request_status($item) }}</td>

                                                <td>{{ $item->branch_name }}</td>
                                                <?php $creditor_obj = json_decode(@$item->creditor_obj); ?>
                                                <td>{{ @$creditor_obj->name }}</td>
                                                <td>
                                                    @if($item->currency == 'KHR')
                                                        {{ number_format(@$item->amount_number) .' ៛'}}
                                                    @else
                                                        {{'$ '. number_format((@$item->amount_number), 2) }}
                                                    @endif
                                                </td>
                                                <td>{{ $item->interest }}</td>
                                                <td>{{ $item->requester_name }}</td>
                                                <td>
                                                    {{ @reviewer_position(\App\BorrowingLoan::reviewerNames($item->id)) }}
                                                </td>
                                                <td>
                                                    {{ @approver_position(\App\BorrowingLoan::approverName($item->id)) }}
                                                </td>
                                                <td>{{ created_at($item->created_at) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="23">Record Not Found!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- {!! $data->render() !!} -->
                    {{ $data->appends($_GET)->links() }}
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
@endsection

@section('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });

            var token = $("#token").val();
            $('.sidebar-mini').addClass("sidebar-collapse");

            $(".btn-delete").on('click', function () {
                var id = $(this).data('item-id');
                var formAction = $(this).attr('action');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: formAction,
                            method:"post",
                            type: "json",
                            data: {
                                "id": id,
                                "_token": token,
                            },
                            success:function (data) {
                                if (data.success == 1) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    );
                                    window.location.reload();
                                } else {
                                    console.log(data.msg);
                                    Swal.fire(
                                        {
                                            title: 'Delete failed !',
                                            text: "Please refresh your browser!",
                                            icon: 'warning',
                                        }
                                    )
                                }
                            },
                            fail:function (err) {
                                console.log(err);
                            }
                        });
                    }
                })
            })
        });
    </script>
@endsection
