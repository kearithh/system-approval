@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('branch.update', $branch) }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
                  @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Branch') }}</h4>
                <div class="text-right">
                  <a href="{{ route('branch.index') }}" class="btn btn-sm btn-success">{{ __('Back to list') }}</a>
                </div>
              </div>
              <div class="card-body ">
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ក្រុមហ៊ុន') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                      <select class="form-control position-select2" name="company_id" required>
                        <option value="0">---</option>
                        @foreach($companies as $item)
                          <option value="{{ $item->id }}" 
                                  @if ($item->id == $branch->company_id)
                                    selected
                                  @endif
                          >{{ $item->name }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('company_id'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('company_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('កូដ') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('code') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}"
                              name="code"
                              id="input-code"
                              type="text"
                              placeholder="Branch Code"
                              value="{{ old('code', $branch->code) }}"
                              aria-required="true"
                      />
                      @if ($errors->has('code'))
                        <span id="code-error" class="error text-danger" for="input-code">{{ $errors->first('code') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ឈ្មោះសាខាកាត់') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('short_name') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('short_name') ? ' is-invalid' : '' }}"
                              name="short_name"
                              id="input-short_name"
                              type="text"
                              placeholder="{{ __('Short Name') }}"
                              value="{{ old('short_name', $branch->short_name) }}"
                              aria-required="true"
                      />
                      @if ($errors->has('short_name'))
                        <span id="short_name-error" class="error text-danger" for="input-short_name">{{ $errors->first('short_name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ឈ្មោះសាខា(KM)') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name_km') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('name_km') ? ' is-invalid' : '' }}"
                              name="name_km"
                              id="input-name_km"
                              type="text"
                              placeholder="{{ __('Name KM') }}"
                              value="{{ old('name_km', $branch->name_km) }}"
                              aria-required="true"
                      />
                      @if ($errors->has('name_km'))
                        <span id="name-error" class="error text-danger" for="input-name_km">{{ $errors->first('name_km') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ឈ្មោះសាខា(EN)') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name_en') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('name_en') ? ' is-invalid' : '' }}"
                              name="name_en"
                              id="input-name_en"
                              type="name_en"
                              placeholder="{{ __('Name EN') }}"
                              value="{{ old('name_en', $branch->name_en) }}"
                      />
                      @if ($errors->has('name_en'))
                        <span id="name_en-error" class="error text-danger" for="input-name_en">{{ $errors->first('name_en') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
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