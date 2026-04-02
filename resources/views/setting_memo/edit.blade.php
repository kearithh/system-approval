@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('setting_memo.update', $data) }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Edit Setting Memo</h4>
                <div class="text-right">
                  <a href="{{ route('setting_memo.index') }}" class="btn btn-sm btn-success">Back to list</a>
                </div>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="form-group col-sm-7">
                    <select class="form-control select2" id="company_id" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}" @if($value->id == $data->company_id) seleted @endif>{{ $value->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">លេខលំដាប់ចាប់ផ្តើម<span class="text-danger">*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input
                              class="form-control"
                              name="no"
                              id="input-level"
                              type="number"
                              min="1"
                              placeholder="No"
                              required="true"
                              aria-required="true"
                              value="{{ $data->no }}"
                      />
                    </div>
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
  <script>
      $(".position-select2").select2({
          tags: true
      });
  </script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>

  <script>
      @if(session('status'))
      Swal.fire({
          title: 'Plese Try agian',
          icon: 'danger',
          timer: '2000',
      })
      @endif
  </script>
@endpush