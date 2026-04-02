@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('plugins.Pace', true)

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">
                MEMO Summary
              </h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-lg-4 col-6">
                  <!-- small box -->
                  <div class="small-box bg-info mb-0">
                    <div class="inner">
                      <h3>{{ sprintf("%02d", $report['total_request']) }}</h3>
{{--                      <h6>$ {{ number_format($report['total_price'], 2) }}</h6>--}}

                      <p>Your total request</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    {{--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
                  </div>
                </div>
                <!-- ./col -->

                <!-- ./col -->
                <div class="col-lg-4 col-6">
                  <!-- small box -->
                  <div class="small-box bg-success mb-0">
                    <div class="inner">
                      <h3>{{ sprintf("%02d", $report['total_request_approve']) }}</h3>
{{--                      <h6>$ {{ number_format($report['total_request_approve_price'], 2) }}</h6>--}}
                      <p>Your total request was approved</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    {{--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
                  </div>
                </div>
                <!-- ./col -->

                <div class="col-lg-4 col-6">
                  <!-- small box -->
                  <div class="small-box bg-orange mb-0">
                    <div class="inner">
                      <h3>{{ sprintf("%02d", $report['total_request_pending']) }}</h3>
{{--                      <h6>$ {{ number_format($report['total_request_pending_price'], 2) }}</h6>--}}
                      <p>Your total request is pending</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-person-add"></i>
                    </div>
                    {{--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>--}}
                  </div>
                </div>
                <!-- ./col -->
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">Search and filter</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('report.memo.search')
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          @include('report.memo.request_list')
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