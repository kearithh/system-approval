@extends('adminlte::page', ['activePage' => @$_GET['company'], 'titlePage' => __('Dashboard')])

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
      <div class="row">
        <div class="col-sm-12">
          @include('president.partials.navigation_empty')
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 col-lg-2">
          @include('president.partials.nav_type')
        </div>
        <div class="col-md-9 col-lg-10">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title ">{{ __('Please Select Type Request') }}</h4>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <!-- <script>
    $('.sidebar-mini').addClass("sidebar-collapse");
  </script> -->
@endpush
