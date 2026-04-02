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
            <?php

                if(basename(Request::url())=='approved') {
                    $approved = 1;
                }

            ?>

            @include('president.partials.nav_department', ['$approved' => @$approved])

            <div class=" @if(@$approved) col-md-8 col-lg-9  @else col-md-12 col-lg-12 @endif">

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title "><strong>Cash Advance List</strong></h4>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                  <table class="table table-striped table-hover">
                    <thead class="">
                      <th>ល.រ</th>
                      <th class="" style="min-width: 130px">{{ __('សកម្ម') }}</th>
                      <th>{{ __('ស្ថានភាព') }}</th>
                      <th class="text-center" style="min-width: 120px;">{{ __('កូដ') }}</th>
                      <th style="min-width: 400px"> បុរេប្រទាន (Advance) </th>
                      <th style="min-width: 152px;">{{ __('ស្នើសំុដោយ') }}</th>
                      <th style="min-width: 320px">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}</th>
                      <th style="min-width: 240px">{{ __('អនុម័តដោយ') }}</th>
                      <th style="min-width: 320px">{{ __('ចម្លងជូន') }}</th>
                      <th style="min-width: 100px">ថ្ងៃស្នើ</th>
                    </thead>
                    <tbody>
                      @if($data->count())
                        <?php $i = 1; ?>
                        @foreach($data as $key => $item)
                          <tr>
                            <td> {{ $i++  }}</td>
                            <td class="td-actions">
                              @include('global.list_action', ['uri' => 'cash_advance', 'object' => $item, 'type' => config('app.type_cash_advance')])
                              @if($item->clear_status != config('app.cash_clear') && $item->status == config('app.approve_status_approve') && @$item->user_id == auth()->id())
                                <button 
                                  class="btn btn-xs btn-primary btn-clear" 
                                  data-action="{{ route('cash_advance.clear', @$item->id) }}" 
                                  title="Clear Cash Advance">
                                  <i class="fa fa-check"></i>
                                </button>
                              @else
                                @if($item->clear_status == config('app.cash_clear'))
                                  <button class="btn btn-xs btn-primary" disabled title="Cash Advance Cleared">
                                    <i class="fa fa-check"></i>
                                  </button>
                                @else
                                  <button class="btn btn-xs btn-primary" disabled title="Waiting">
                                    <i class="fa fa-check"></i>
                                  </button>
                                @endif
                              @endif
                            </td>
                            <td>
                              {{ request_status($item) }}
                            </td>
                            <td class="text-center">{{ @showArrayCode($item->code) }}</td>
                            <td>
                              {{ @$item->title }}
                            </td>
                            <td>
                              {{ $item->requester_name }}
                            </td>
                            <td>
                              {{ reviewer_position(\App\CashAdvance::reviewerName($item->id)) }}
                            </td>
                            <td>
                              {{ @approver_position(\App\CashAdvance::approverName($item->id)) }}
                            </td>
                            <td>
                              {{ reviewer_position(\App\CashAdvance::ccName($item->id)) }}
                            </td>
                            <td>
                              {{ created_at($item->created_at) }}
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

  <div class="modal fade" id="clear_modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="clear_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><b>Clear Cash Advance</b></h6>
            </div>
            <div class="modal-body">
                <form method="POST" id="form-clear" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">កំណត់សំគាល់ <span style='color: red'>*</span></label>
                        <textarea required name="remark" class="form-control" placeholder="កំណត់សំគាល់"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">ឯកសារភ្ជាប់ <span style='color: red'>*</span></label>
                        <input type="file" required name="file[]">
                    </div>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" class="btn btn-success btn-sm">Submit</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
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

            $(".btn-clear" ).on( "click", function( event ) {
              $action = $(this).data('action');
              $('#form-clear').attr('action', $action);
              $('#clear_modal').modal('show');
            });
            
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
