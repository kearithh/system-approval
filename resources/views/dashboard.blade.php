@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

<!-- loanding page -->
@section('plugins.Pace', true)

@section('content')
          <div class="container-fluid">
              <div class="row">
                  <div class="col-lg-3 col-6">
                      <!-- small box -->
                      <a href="#">
                        <div class="small-box bg-info">
                          <div class="inner">
                              <h3>{{ $totalRequests }}</h3>

                              <p>Requests</p>
                          </div>
                          <div class="icon">
                              <i class="fas fa-paper-plane"></i>
                          </div>
                        </div>
                      </a>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                      <!-- small box -->
                      <a href="{{ route('user.index') }}">
                        <div class="small-box bg-success">
                          <div class="inner">
                              <h3>{{ $totalStaffs }}</h3>

                              <p>Staffs Active</p>
                          </div>
                          <div class="icon">
                              <i class="fas fa-users"></i>
                          </div>
                        </div>
                      </a>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                      <!-- small box -->
                      <a href="{{ route('department.index') }}">
                        <div class="small-box bg-warning">
                          <div class="inner">
                              <h3>{{ count($departments) }}</h3>

                              <p>Departments</p>
                          </div>
                          <div class="icon">
                              <i class="fas fa-building"></i>
                          </div>
                        </div>
                      </a>
                  </div>
                  <!-- ./col -->
                  <div class="col-lg-3 col-6">
                      <!-- small box -->
                      <a href="{{ route('position.index') }}">
                        <div class="small-box bg-danger">
                          <div class="inner">
                              <h3>{{ $totalPositions }}</h3>

                              <p>Positions</p>
                          </div>
                          <div class="icon">
                              <i class="fas fa-crosshairs"></i>
                          </div>
                        </div>
                      </a>
                  </div>
                  <!-- ./col -->
              </div>


              <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->

@endsection

