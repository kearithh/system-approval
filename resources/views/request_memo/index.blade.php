@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('plugins.Pace', true)
@push('css')
<style>
    .table td, .table th {
        padding: .75rem .3rem;
    }
</style>
@endpush

@section('content')
  <div class="content">
    <div class="container-fluid">
      <!-- /.row -->
      <div class="row hidden">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
{{--              <h3 class="card-title">Search and filter</h3>--}}
                @if (\Illuminate\Support\Facades\Auth::user()->position->level != config('app.position_level_president'))
                    <a class="btn btn-xs bg-orange" href="/request_memo?status=1&type=2" style="font-size: 0.85rem;">
                        Your Pending Request
                        <span class="badge badge-light">{{ $report['total_request_pending'] }}</span>
                    </a>
                @endif
                <a class="btn btn-xs bg-info" href="/request_memo?status=1&type=3" style="font-size: 0.85rem;">
                    Your Approval Request
                    <span class="badge badge-light">{{ @$memo_approval }}</span>
                </a>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('global.search', ['clear_url' => 'request_memo'])
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          @include('request_memo.partials.table')
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        @if(session('status'))
        Swal.fire({
            title: 'Success',
            icon: 'success',
            timer: '2000',
        });
        @endif
    </script>
    <script>
    $(document).ready(function() {

        // $('#post_date_from').datetimepicker();
        // $('#post_date_to').datetimepicker();

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
        });

        $(".btn-delete").on('click', function () {
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
                            "_token": "{{ @csrf_token() }}",
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


    //Datemask dd/mm/yyyy
    $('#start_date').inputmask()
    $('#end_date').inputmask()
  </script>
@endpush
