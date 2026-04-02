@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('css')
  <style>
    .table td {
      vertical-align: middle;
    }
  </style>
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">

      <div class="row hidden">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
                @if (\Illuminate\Support\Facades\Auth::user()->position->level != config('app.position_level_president'))
                    <a class="btn btn-xs bg-orange" href="/request?status=1&type=2" style="font-size: 0.85rem;">
                        Your Pending Request
                        <span class="badge badge-light">{{ $totalPendingRequest }}</span>
                    </a>
                @endif
              <a class="btn btn-xs bg-info" href="/request?status=1&type=3" style="font-size: 0.85rem;">
                Your Approval Request
                <span class="badge badge-light">{{ $viewShare['se_approval'] }}</span>
              </a>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
{{--              @include('request.search')--}}
              @include('global.search',['clear_url' => 'request'])
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
              <h4 class="card-title ">{{ __('Special Request List') }}</h4>
              <div class="text-right">
                <a href="{{ route('request.create') }}" class="btn btn-sm btn-success">{{ __('Create') }}</a>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table">
                <thead class="">
                <th>ល.រ</th>
                <th class="" style="min-width: 100px">{{ __('សកម្ម') }}</th>
                <th>{{ __('ស្ថានភាព') }}</th>
                <th style="min-width: 250px">{{ __('កម្មវត្ថុ') }}</th>
                <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                <th style="min-width: 320px">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}</th>
                <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                <th style="min-width: 102px">{{ __('តម្លៃសរុប') }}</th>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                @foreach($data as $key => $item)
                  <tr>
                    <td> {{ $i++  }}</td>
                    <td class="td-actions">
                        @include('global.list_action', ['uri' => 'request', 'object' => $item])
                    </td>
                    <td>
                      {{ request_status($item) }}
                    </td>
                    <td>
                        {{ $item->purpose }}
                    </td>
                    <td>
                        {{ $item->requester_name }}
                    </td>
                    <td>
                    {{ reviewer_position(\App\RequestForm::reviewerName($item->id)) }}
                    </td>
                    <td>
                        {{ created_at($item->created_at) }}
                    </td>
                    <td class="text-right">
                      {{'$ '. number_format(($item->total_amount_usd),2) }}<br>
                      {{ number_format(($item->total_amount_khr),2) .' ៛'}}
                    </td>
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
@endsection

@section('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
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
                                _token: "{{ csrf_token() }}",
                            },
                            success:function (data) {
                                if (data.status == 1) {
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
