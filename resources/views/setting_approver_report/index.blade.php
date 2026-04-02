@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('setting-approver-report.update') }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Approver on Report</h4>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-2">
                    <label>Staff Approver<span style='color: red'>*</span></label>
                  </div>
                  <div class="form-group col-sm-7">
                    <select class="form-control values select2" name="values[]" required multiple>
                      @foreach($staff_approver as $key => $value)
                        <option value="{{ $value->id }}" selected>{{ $value->staff_name }}</option>
                      @endforeach
                      @foreach($staff_not_approver as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->staff_name }}</option>
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
      
      @if(session('status') == 1)
          Swal.fire({
            title: 'Update Success',
            icon: 'success',
            timer: '5000',
          })
      @elseif(session('status') == 2)
          Swal.fire({
            title: 'Please Try agian',
            icon: 'error',
            timer: '5000',
          })
      @endif
  </script>
@endpush