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

      <div class="hidden row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
                @if (\Illuminate\Support\Facades\Auth::user()->position->level != config('app.position_level_president'))
                    <a class="btn btn-xs bg-orange" href="/request_hr?status=1&type=2" style="font-size: 0.85rem;">
                        Your Pending Request
                        <span class="badge badge-light">{{ $totalPendingRequest }}</span>
                    </a>
                @endif
              <a class="btn btn-xs bg-info" href="/request_hr?status=1&type=3" style="font-size: 0.85rem;">
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
              @include('global.search', ['clear_url' => 'request_hr'])
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
              <h4 class="card-title ">{{ __('General Expense List') }}</h4>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped">
                <thead class="">
                <th style="min-width: 50px">ល.រ</th>
                <th class="" style="width: 100px">{{ __('សកម្ម') }}</th>
                <th>{{ __('ស្ថានភាព') }}</th>
                <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                <th>{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}</th>
                <th>ថ្ងៃស្នើ</th>
                <th style="min-width: 102px">{{ __('តម្លៃសរុប') }}</th>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                @foreach($data as $key => $item)
                  <tr>
                    <td> {{ $i++ }}</td>
                    <td class="td-actions">
                        @include('global.list_action', ['uri' => 'request_hr', 'object' => $item, 'type' => config('app.type_general_expense')])
                    </td>
                    <td>{{ request_status($item) }}</td>
                    <td>{{ $item->requester_name }}</td>
                    <td>
                      {{ reviewer_position(\App\RequestHR::reviewerName($item->id)) }}
                    </td>
                    <td>{{ created_at($item->created_at) }}</td>
                    <td class="text-right">
                      @if($item->total > 0 )
                        {{'$ '. number_format(($item->total),2) }}<br>
                      @endif
                      @if($item->total_khr > 0 )
                        {{ number_format($item->total_khr) .' ៛'}}
                      @endif
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
