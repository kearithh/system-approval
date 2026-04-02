@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('plugins.Pace', true)

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
      </div>
      <!-- /.row -->
      <div class="row">
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
          @include('request_disposal.partials.list_section')
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    // $(document).ready(function() {
    //   // Javascript method's body can be found in assets/js/demos.js
    //   md.initDashboardPageCharts();
    // });

    // $('#start_date').datetimepicker();

    //Datemask dd/mm/yyyy
    $('#start_date').inputmask()
    $('#end_date').inputmask()
  </script>
@endpush
