@extends('adminlte::page', ['activePage' => 'position', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('position.update', $position) }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Edit Position</h4>
                <div class="text-right">
                  <a href="{{ route('position.index') }}" class="btn btn-sm btn-success">Back to list</a>
                </div>
              </div>
              <div class="card-body ">
                <div class="row">
                  <label class="col-sm-2 col-form-label">ឈ្មោះកាត់</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('short_name') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('short_name') ? ' is-invalid' : '' }}"
                              name="short_name"
                              id="input-short_name"
                              type="text"
                              placeholder="{{ __('Short Name') }}"
                              value="{{ old('short_name', $position->short_name) }}"
                      />
                      @if ($errors->has('short_name'))
                        <span id="short_name-error" class="error text-danger" for="input-short_name">{{ $errors->first('short_name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">ឈ្មោះ(KM)</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name_km') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('name_km') ? ' is-invalid' : '' }}"
                              name="name_km"
                              id="input-name_km"
                              type="text"
                              placeholder="{{ __('Khmer Name') }}"
                              value="{{ old('name_km', $position->name_km) }}"
                      />
                      @if ($errors->has('name_km'))
                        <span id="name_km-error" class="error text-danger" for="input-name_km">{{ $errors->first('name_km') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">ឈ្មោះ(EN)</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name_en') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('name_en') ? ' is-invalid' : '' }}"
                              name="name_en"
                              id="input-name_en"
                              type="text"
                              placeholder="{{ __('English Name') }}"
                              value="{{ old('name_en', $position->name_en) }}"
                      />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">លំដាប់</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('level') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('level') ? ' is-invalid' : '' }}"
                              name="level"
                              id="input-level"
                              type="number"
                              min="1"
                              max="1000"
                              placeholder="{{ __('Level') }}"
                              value="{{ old('level', $position->level) }}"
                      />
                      @if ($errors->has('level'))
                        <span id="level-error" class="error text-danger" for="input-level">{{ $errors->first('level') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">បរិយាយ</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">
                      <textarea 
                              class="form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}"
                              name="desc"
                              id="input-desc"
                      >
                        {{ old('desc', $position->desc) }}
                      </textarea>
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