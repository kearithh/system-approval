@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
            method="post"
            action="{{ route('setting_group_support.update', 1) }}"
            enctype="multipart/form-data"
            autocomplete="off"
            class="form-horizontal">
            
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Edit Setting Group Support</h4>
                <div class="text-right">
                  <a href="{{ route('setting_group_support.index') }}" class="btn btn-sm btn-success">Back to list</a>
                </div>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-2">
                    <label>ឈ្មោះបុគ្គលិក<span style='color: red'>*</span></label>
                  </div>
                  <div class="form-group col-sm-7">
                    <select class="form-control select2" required multiple name="value[]">
                      @foreach($staff_use as $item)
                        <option value="{{ $item->id }}" selected="selected">{{ $item->staff_name }}</option>
                      @endforeach
                      @foreach($staff as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->staff_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <label>ឈ្មោះនាយកដ្ឋាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="form-group col-sm-7">
                    <select class="form-control select2" required multiple name="department[]">
                      @foreach($department_use as $item)
                        <option value="{{ $item->id }}" selected="selected">{{ $item->name_km }}</option>
                      @endforeach
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->name_km }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(".position-select2").select2({
      tags: true
    });

    @if(session('status'))
      Swal.fire({
        title: 'Plese Try agian',
        icon: 'danger',
        timer: '2000',
      })
    @endif
  </script>
@endpush