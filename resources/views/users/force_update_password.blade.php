@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('password.update') }}"
                  autocomplete="off"
                  enctype="multipart/form-data"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Update Password') }}</h4>
                  <div class="text-right">
                    <a href="{{ url('user') }}" class="btn btn-sm btn-success">
                      {{ __('Back') }}
                    </a>
                  </div>
              </div>
              <div class="card-body ">
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-current-password">{{ __(' លេខសំងាត់​បច្ចុប្បន្ន') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('current-password') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('current-password') ? ' is-invalid' : '' }}"
                              type="password"
                              name="current-password"
                              id="input-current-password"
                              placeholder="{{ __('Current Password') }}"
                              value="{{ old('current-password') }}"
                      />
                      @if ($errors->has('current-password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('current-password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-new-password">{{ __(' លេខសម្ងាត់ថ្មី') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('new-password') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('new-password') ? ' is-invalid' : '' }}"
                              type="password"
                              name="new-password"
                              id="input-new-password"
                              placeholder="{{ __('New Password') }}"
                              value="{{ old('new-password') }}"
                      />
                      @if ($errors->has('new-password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('new-password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-confirm-password">{{ __('បញ្ជាក់លេខសម្ងាត់ថ្មី') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('confirm-password') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('confirm-password') ? ' is-invalid' : '' }}"
                              name="confirm-password"
                              id="input-confirm-password"
                              type="password"
                              placeholder="{{ __('Confirm New Password') }}"
                              value="{{ old('confirm-password') }}"
                      />
                      @if ($errors->has('confirm-password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('confirm-password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success">{{ __('Change') }}</button>
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
@endpush