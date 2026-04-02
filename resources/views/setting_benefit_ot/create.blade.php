@extends('adminlte::page', ['activePage' => 'ផosition', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('benefit_ot.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Create Benefit OT</h4>
                <div class="text-right">
                  <a href="{{ route('benefit_ot.index') }}" class="btn btn-sm btn-success">Back to list</a>
                </div>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="form-group col-sm-7">
                    <select class="form-control select2" id="company_id" name="company_id" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($company as $key => $value)
                        @if($value->id==Auth::user()->company_id)
                          <option value="{{ $value->id }}" selected="selected">{{ $value->name }}</option>
                        @else
                          <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>
                
                <div class="row">
                  <label class="col-sm-2 col-form-label">ប្រភេទ<span class="text-danger">*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <select class="form-control select2" name="type" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach(config('app.benefit_type') as $key => $value)
                          <option value="{{ $value->val }}">
                            {{ $value->name_km }} | {{ $value->name_en }}
                          </option>
                        @endforeach
                    </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">អត្ថប្រយោជន៍ (%)<span class="text-danger">*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input
                              class="form-control"
                              name="benefit"
                              type="number"
                              min="1"
                              placeholder="%"
                              required="true"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success">Submit</button>
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