@extends('adminlte::page', ['activePage' => 'request_pr', 'titlePage' => __('Request')])

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
                    <a class="btn btn-xs bg-orange" href="/request_pr?status=1&type=2" style="font-size: 0.85rem;">
                        Your Pending Request
                        <span class="badge badge-light">{{ @$totalPendingRequest }}</span>
                    </a>
                @endif
              <a class="btn btn-xs bg-info" href="/request_pr?status=1&type=3" style="font-size: 0.85rem;">
                Your Approval Request
                <span class="badge badge-light">{{ @$totalPendingReview }}</span>
              </a>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
{{--              @include('request_pr.search')--}}
              @include('global.search',['clear_url' => 'request_pr'])
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

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
                  <h4 class="card-title "><strong>PR Request List</strong></h4>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped table-hover">
                    <thead class="">
                    <th>ល.រ <br> No</th>
                    <th class="" style="min-width: 100px">សកម្ម <br> Setting</th>
                    <th>ស្ថានភាព <br> status</th>
                    <th class="text-center" style="min-width: 120px;">កូដ <br> Code</th>
                    <th style="min-width: 250px">កម្មវត្ថុ <br> Purpose</th>
                    <th style="min-width: 152px;">ស្នើសំុដោយ <br> Request by</th>
                    <th style="min-width: 320px">ពិនិត្យ និងបញ្ជូនបន្តដោយ <br> Verified by</th>
                    <th style="min-width: 240px">
                      អនុម័តដោយ<br> Approved by
                    </th>
                    <th style="min-width: 100px">ថ្ងៃស្នើ <br> Request date</th>
                    
                    </thead>
                    <tbody>
                      @if($data->count())
                        <?php $i = 1; ?>
                        @foreach($data as $key => $item)
                        @php
                        //dd($data); 
                        @endphp
                          <tr>
                            <td> {{ $i++  }}</td>
                            <td class="td-actions">
                              @include('global.list_action', ['uri' => 'request_pr', 'object' => $item, 'type' => config('app.type_pr_request')])
                            </td>
                            <td>
                              {{ request_status($item) }}
                            </td>
                            <td class="text-center">
                              {{ @$item->department_name_en }}-{{ $item->code }}
                            </td>
                            <td>
                              {{ $item->purpose }}
                            </td>
                            <td>
                              {{ $item->requester_name }}
                            </td>
                            <td>
                              {{ reviewer_position(\App\RequestPR::reviewerName($item->id)) }}
                            </td>
                            <td>
                              {{ @approver_position(\App\RequestPR::approverName($item->id)) }}
                            </td>
                            <td>
                              {{ created_at($item->created_at) }}
                            </td>
                            
                          </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="10">Record Not Found!</td>
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
