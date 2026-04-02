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

            <div class="hidden row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            @if (\Illuminate\Support\Facades\Auth::user()->position->level != config('app.position_level_president'))
                                <a class="btn btn-xs bg-orange" href="/disposal?status=1&type=2" style="font-size: 0.85rem;">
                                    Your Pending Request
                                    <span class="badge badge-light">{{ $totalPendingRequest }}</span>
                                </a>
                            @endif
                            <a class="btn btn-xs bg-info" href="/disposal?status=1&type=3" style="font-size: 0.85rem;">
                                Your Approval Request
                                <span class="badge badge-light">{{ $totalPendingApproval }}</span>
                            </a>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.card-tools -->
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('global.search', ['clear_url' => 'disposal'])
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title ">{{ __('Damaged Asset List') }}</h4>
                        </div>
                        <div class="table-responsive" style="padding: 0 10px">
                            <table class="table table-striped">
                                <thead class="">
                                    <tr>
                                        <th style="min-width: 50px">​ល.រ</th>
                                        <th class="" style="min-width: 100px">{{ __('សកម្មភាព') }}</th>
                                        <th style="min-width: 100px;">{{ __('ស្ថានភាព') }}</th>
                                        <th style="min-width: 200px;">បរិយាយមូលហេតុ</th>
                                        <th style="min-width: 100px;">{{ __('សំណង') }}</th>
                                        <th style="min-width: 100px">{{ __('ស្នើរដោយ') }}</th>
                                        <th style="min-width: 215px">{{ __('ពិនិត្យដោយ') }}</th>
                                        <th style="min-width: 245px; vertical-align: top">{{ __('អនុម័តដោយ') }}</th>
                                        <th style="min-width: 150px">{{ __('ថ្ងៃស្នើរ') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $key => $item)
                                        <tr>
                                            <td> {{ $key +1  }}</td>
                                            <td class="td-actions">
                                                @include('global.list_action', ['uri' => 'damagedlog', 'object' => $item])
                                            </td>
                                            <td>{{ memo_status($item) }}</td>
                                            <td><?php echo ($item->desc )?></td>
                                            @if($item->is_penalty==0)
                                                <td>No</td>
                                            @else
                                                <td>Yes</td>
                                            @endif
                                            <td>{{ $item->requester_name }}</td>
                                            <td>
                                                {{ @reviewer_position(\App\DamagedLog::reviewerNames($item->id)) }}
                                            </td>
                                            <td style="vertical-align: top">
                                                {{ @approver_position(\App\DamagedLog::approverName($item->id)) }}
                                            </td>
                                            <td>{{ $item->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {!! $data->render() !!}
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
