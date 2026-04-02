@extends('adminlte::page', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

<!-- loanding page -->
@section('plugins.Pace', true)

@section('content')
    <div class="container-fluid">
        <div class="row mb-2 breadcrumb">
            <div class="col-sm-6">
                <a href="{{ route('dashboard') }}">
                  <h5 class="m-0"><i class="fas fa-fw fa-home"></i> Dashboard</h5>
                </a>
            </div>
        </div>

        <div class="text-center">
            <br>
            <h5 style="color: red">សំណើនេះត្រូវបានលុបចោល</h5>
            <h5 style="color: red">The Request has been deleted.</h5>
        </div>
    </div>
@endsection

@section('plugins.Chartjs', true)
@push('js')
  <script>

    @if(session('status')==2)
        Swal.fire({
          title: 'Update Success',
          icon: 'success',
          timer: '1000',
        })
    @endif

  </script>
@endpush
