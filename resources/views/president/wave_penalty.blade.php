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

            <div class=" @if(@$approved) col-md-8 col-lg-9  @else col-md-12 col-lg-12 @endif">

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title "><strong>Wave Penalty List</strong></h4>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped table-hover">
                    <thead class="">
                    <th>ល.រ</th>
                    <th class="" style="min-width: 100px">{{ __('សកម្ម') }}</th>
                    <th>{{ __('ស្ថានភាព') }}</th>
                    <th style="min-width: 250px">{{ __('កម្មវត្ថុ') }}</th>
                    <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                    <th style="min-width: 320px">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}</th>
                    <th style="min-width: 240px">
                      {{ __('អនុម័តដោយ') }}
                    </th>
                    <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                    <th style="min-width: 130px" class="text-right">{{ __('តម្លៃសរុប') }}</th>
                    </thead>
                    <tbody>
                      @if($data->count())
                        <?php $i = 1; ?>
                        @foreach($data as $key => $item)
                          <tr>
                            <td> {{ $i++  }}</td>
                            <td class="td-actions">
                              @include('global.list_action', ['uri' => 'penalty', 'object' => $item, 'type' => config('app.type_penalty')])
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
                              {{ reviewer_position(\App\Penalty::reviewerName($item->id)) }}
                            </td>
                            <td>
                              {{ @approver_position(\App\Penalty::approverName($item->id)) }}
                            </td>
                            <td>
                              {{ created_at($item->created_at) }}
                            </td>
                            <td class="text-right">
                              @if($item->total_amount_usd > 0 )
                                {{'$ '. number_format(($item->total_amount_usd),2) }}<br>
                              @endif
                              @if($item->total_amount_khr > 0 )
                                {{ number_format($item->total_amount_khr) .' ៛'}}
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="9">Record Not Found!</td>
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
@endsection

@section('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".preview").on('click', function () {
                localStorage.previous = window.location.href ;
            });

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
