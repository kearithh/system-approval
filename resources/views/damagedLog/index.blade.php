@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('plugins.Pace', true)

@section('content')
  <div class="content">
    <div class="container-fluid">

      <div class="row hidden">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
              <div class="card-header">
                  <a class="btn btn-xs bg-orange" href="/request_dispose?status=1&type=2" style="font-size: 0.85rem;">
                      Your Pending Request
                      <span class="badge badge-light">{{ $totalPendingRequest }}</span>
                  </a>
                  <a class="btn btn-xs bg-info" href="/request_dispose?status=1&type=3" style="font-size: 0.85rem;">
                      Your Approval Request
                      <span class="badge badge-light">{{ @$viewShare['disposal_approval'] }}</span>
                  </a>

                  <div class="card-tools">
                      <button type="button" class=" btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                      </button>
                  </div>
                  <!-- /.card-tools -->
              </div>

              <!-- /.card-header -->
            <div class="card-body">
              @include('global.search')
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title ">{{ __('Damaged Asset List') }}</h4>
                <div class="text-right">
                    <a href="{{ route('damagedlog.create') }}" class="btn btn-sm btn-success">{{ __('Create') }}</a>
                </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table">
                <thead class="">
                  <tr>
                    <th style="min-width: 50px">​ល.រ</th>
                    <th class="" style="min-width: 100px">{{ __('សកម្មភាព') }}</th>
                    <th style="min-width: 100px;">{{ __('ស្ថានភាព') }}</th>
                    <th style="min-width: 200px;">បរិយាយមូលហេតុ</th>
                    <th style="min-width: 100px;">{{ __('សំណង') }}</th>
                    <th style="min-width: 215px">{{ __('ស្នើរដោយ') }}</th>
                    <th style="min-width: 215px">{{ __('ពិនិត្យដោយ') }}</th>
                    <th style="min-width: 245px; vertical-align: top">{{ __('អនុម័តដោយ') }}</th>
                    <th style="min-width: 150px">{{ __('ថ្ងៃស្នើរ') }}</th>
                  </tr>
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
                      <td>{{ @reviewer_position(\App\DamagedLog::reviewerNames($item->id)) }}</td>
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
@endsection

@push('js')
  <script>
    $('#start_date').inputmask()
    $('#end_date').inputmask()
  </script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        @if(session('status'))
        Swal.fire({
            title: 'Success',
            icon: 'success',
            timer: '2000',
        })
        @endif
    </script>
    <script>
      $(document).ready(function() {

            $("form.delete_form" ).on( "click", function( event ) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    // text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {
                        $(this).submit();
                    }
                })
            });
        });
    </script>
@endpush
