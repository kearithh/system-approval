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
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title ">
                <strong>Branch Request (FN) List</strong>
              </h4>

              <?php
                  $url = basename(Request::url());
                  $company = @$_GET['company'];
                  $type = @$_GET['type'];
                  $department = @$_GET['department'];
                  $date_from = @$_GET['date_from'];
                  $date_to = @$_GET['date_to'];
              ?>
              <div style="float: right;">
                <form
                      method="GET"
                      action="{{ $url }}"
                      class="form-horizontal">
                  @csrf

                  <input type="hidden" name="company" value="{{ @$company }}">
                  <input type="hidden" name="type" value="{{ @$type }}">
                  <input type="hidden" name="department" value="{{ @$department }}">

                  <table>
                    <td style="vertical-align: middle;">
                      <strong>Filter:</strong>
                    </td>
                    <td>
                      <input type="text" name="date_from" autocomplete="off" placeholder="From" value="{{ @$date_from }}" class="form-control form-control-sm datepicker">
                    </td>
                    <td>
                      <input type="text" name="date_to" autocomplete="off" placeholder="To" value="{{ @$date_to }}" class="form-control form-control-sm datepicker">
                    </td>
                    <td>
                      <button type="submit" name="" class="btn btn-sm btn-info">
                        <i class="fas fa-search"></i>
                        Find
                      </button>
                    </td>
                  </table>

                </form>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                <th>ល.រ</th>
                <th style="min-width: 100px">សកម្ម</th>
                <th style="min-width: 80px">ស្ថានភាព</th>
                <th style="min-width: 120px" class="text-center">កូដ</th>
                <th style="min-width: 250px">ប្រភេទ</th>
                <th style="min-width: 250px">កម្មវត្ថុ</th>
                <th style="min-width: 152px">ស្នើសំុដោយ</th>
                <th style="min-width: 320px">ពិនិត្យ និងបញ្ជូនបន្តដោយ</th>
                <th style="min-width: 240px">អនុម័តដោយ</th>
                <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                <!-- <th style="min-width: 130px" class="text-right">តម្លៃសរុប</th> -->
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                      <tr>
                        <td> {{ $i++  }}</td>
                        <td class="td-actions">
                          @include('global.list_action', ['uri' => 'general_request', 'object' => $item, 'type' => config('app.type_general_request')])
                        </td>
                        <td>
                          {{ request_status($item) }}
                        </td>
                        <td class="text-center">
                          {{ @showArrayCode($item->code) }}
                        </td>
                        <td>
                          {{ @config('app.branch_request')[@$item->type] }}
                        </td>
                        <td>
                          {{ $item->purpose }}
                        </td>
                        <td>
                          {{ $item->requester_name }}
                        </td>
                        <td>
                          {{ reviewer_position(\App\GeneralRequest::reviewerName($item->id)) }}
                        </td>
                        <td>
                          {{ @approver_position(\App\GeneralRequest::approverName($item->id)) }}
                        </td>
                        <td>
                          {{ created_at($item->created_at) }}
                        </td>
                        <!-- <td class="text-right">
                          @if($item->total_amount_usd > 0 )
                            {{'$ '. number_format(($item->total_amount_usd),2) }}<br>
                          @endif
                          @if($item->total_amount_khr > 0 )
                            {{ number_format($item->total_amount_khr) .' ៛'}}
                          @endif
                        </td> -->
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="11">Record Not Found!</td>
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
