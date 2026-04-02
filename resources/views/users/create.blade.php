@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('content')
<style type="text/css">
  .select2-dropdown {
      z-index:99999;
  }
</style>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('user.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
                  @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Create User') }}</h4>
                <div class="text-right">
                  <a href="{{ route('user.index') }}" class="btn btn-sm btn-success">{{ __('Back to list') }}</a>
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
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ក្រុមហ៊ុន') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                      <select class="form-control myselect2" name="company_id" required>
                        <option value="">---</option>
                        @foreach($companies as $item)
                          <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('company_id'))
                        <span id="name-company_id" class="error text-danger" for="input-name">{{ $errors->first('company_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ការិយាល័យកណ្តាល / សាខា') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('branch_id') ? ' has-danger' : '' }}">
                      <select class="form-control myselect2" name="branch_id" required>
                        <option value="">---</option>
                        @foreach($branch as $item)
                          <option value="{{ $item->id }}">{{ $item->name_km }}({{ $item->short_name }})</option>
                        @endforeach
                      </select>
                      @if ($errors->has('branch_id'))
                        <span id="name-branch_id" class="error text-danger" for="input-name">{{ $errors->first('branch_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('នាយកដ្ឋាន') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('department_id') ? ' has-danger' : '' }}">
                      <select class="form-control myselect2" name="department_id">
                        <option value="">---</option>
                        @foreach($department as $item)
                          <option value="{{ $item->id }}">{{ $item->name_km }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('department_id'))
                        <span id="name-department_id" class="error text-danger" for="input-name">{{ $errors->first('department_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('មុខតំណែង') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select class="form-control position-select2" name="position_id" required>
                        <option value="">---</option>
                        @foreach($positions as $item)
                          <option value="{{ $item->id }}">{{ $item->name_km }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('position_id'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('position_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('គោត្តនាម និងនាម(ខ្មែរ)') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                              name="name"
                              id="input-name"
                              type="text"
                              placeholder="{{ __('គោត្តនាម និងនាម(ខ្មែរ)') }}"
                              value="{{ old('name') }}"
                              required="true"
                              aria-required="true"
                      />
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('អ្នកប្រើប្រាស') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                              name="username"
                              id="input-name"
                              type="text"
                              placeholder="{{ __('Username') }}"
                              value="{{ old('username') }}"
                              required="true"
                              aria-required="true"
                      />
                      @if ($errors->has('username'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('username') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('លេខសំគាល់បុគ្គលិក') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('system_user_id') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('system_user_id') ? ' is-invalid' : '' }}"
                              name="system_user_id"
                              id="input-name"
                              type="number"
                              placeholder="{{ __('System ID') }}"
                              value="{{ old('system_user_id') }}"
                      />
                      @if ($errors->has('system_user_id'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('system_user_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ភេទ') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
                      <select class="form-control" name="gender">
                        <option value="">---</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                      </select>
                      @if ($errors->has('gender'))
                        <span id="name-gender" class="error text-danger" for="input-name">{{ $errors->first('gender') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('អ៊ីម៉ែល') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                              name="email"
                              id="input-email"
                              type="email"
                              placeholder="yourname@sahakrinpheap.com.kh"
                              value="{{ old('email') }}"
                      />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password">{{ __('លេខសម្ងាត់') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                              type="password"
                              name="password"
                              id="input-password"
                              placeholder="{{ __('Password') }}"
                              value="123456"
                              required
                      />
                      @if ($errors->has('password'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('password') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('បញ្ជាក់លេខសម្ងាត់') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <input
                              class="form-control"
                              name="password_confirmation"
                              id="input-password-confirmation"
                              type="password"
                              placeholder="{{ __('Confirm Password') }}"
                              value="123456"
                              required
                      />
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('ហត្ថលេខា') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group form-file-upload {{ $errors->has('signature') ? ' has-danger' : '' }}">
                      <input name="signature" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                    </div>
                    @if ($errors->has('signature'))
                      <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('signature') }}</span>
                    @endif
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('ហត្ថលេខាតូច') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group form-file-upload {{ $errors->has('short_signature') ? ' has-danger' : '' }}">
                      <input name="short_signature" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                    </div>
                    @if ($errors->has('short_signature'))
                      <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('short_signature') }}</span>
                    @endif
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('រូបតំណាង') }}</label>
                  <div class="col-sm-7">
                    <div class="form-group form-file-upload">
                      <input name="avatar" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                    </div>
                  </div>
                  @if ($errors->has('avatar'))
                    <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('avatar') }}</span>
                  @endif
                </div>

                <div class="card" style="padding: 5px 1pc;">
                    <h5><strong for="">Contract Notification</strong></h5>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">{{ __('ប្រភេទមុខងារ') }}</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="role_type">
                                  <option value=""> << Please seelect >> </option>
                                  @foreach (Constants::CONTRACT_MANAGEMEMNT_ROLES as $key => $value)
                                    <option value="{{ $key }}"> {{ $value }} </option>
                                  @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                </div>

              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
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
    $(document).ready(function () {
      $(".position-select2").select2({
          tags: true
      });
      $(".myselect2").select2({

      });
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
