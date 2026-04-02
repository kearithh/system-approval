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

                    <div class="row">
                        <?php

                            if(basename(Request::url())=='approved') {
                                $approved = 1;
                            }

                        ?>

                        @include('president.partials.nav_department', ['$approved' => @$approved])

                        <div class=" @if(@$approved) col-md-8 col-lg-9  @else col-md-12 col-lg-12 @endif"> 

                            <div class="card">
                                <div class="card-header card-header-primary">
                                    <h4 class="card-title "><strong>Sale Asset List</strong></h4>
                                </div>
                                <div class="table-responsive" style="padding: 0 10px">
                                    <table class="table table-striped table-hover">
                                        <thead class="">
                                        <th style="min-width: 50px">ល.រ</th>
                                        <th class="" style="min-width: 100px">{{ __('សកម្ម') }}</th>
                                        <th style="min-width: 50px;">{{ __('ស្ថានភាព') }}</th>
                                        <th style="min-width: 245px; text-align: center;">សុំលក់ទ្រព្យសម្បត្តិ</th>
                                        <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                                        <th style="min-width: 320px;">{{ __('ត្រួតពិនិត្យដោយ') }}</th>
                                        <th style="min-width: 245px;">{{ __('អនុម័តដោយ') }}</th>
                                        <th style="min-width: 175px;">ថ្ងៃស្នើ</th>
                                        </thead>
                                        <tbody>
                                            @if($data->count())
                                                <?php $i = 1; ?>
                                                @foreach($data as $key => $item)
                                                    <tr>
                                                        <td> {{ $i++ }}</td>
                                                        <td class="td-actions">
                                                            @include('global.list_action', ['uri' => 'sale_asset', 'object' => $item, 'type' => config('app.type_sale_asset')])
                                                        </td>
                                                        <td>{{ request_status($item) }}</td>
                                                        <td>
                                                            {{ @items(\App\SaleAsset::items_name($item->id)) }}
                                                        </td>
                                                        <td>{{ $item->requester_name }}</td>
                                                        <td>{{ @reviewer_position(\App\SaleAsset::reviewerNames($item->id)) }}</td>
                                                        <td>{{ @approver_position(\App\SaleAsset::approverName($item->id)) }}</td>
                                                        <td>{{ created_at($item->created_at) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8">Record Not Found!</td>
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

            $('.sidebar-mini').addClass("sidebar-collapse");
            var token = $("#token").val();

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
