@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('user.update', $user) }}"
                  autocomplete="off"
                  enctype="multipart/form-data"
                  class="form-horizontal">
            @csrf
            @method('put')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit User') }}</h4>
                <div class="text-right">
                  <a href="{{ route('password.edit') }}" class="btn btn-sm btn-info">
                    {{ __('Change Your Password') }}
                  </a>
                  <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">{{ __('Home') }}</a>
                </div>
              </div>
              <div class="card-body ">

                @if (session('error'))
                    <div class="alert alert-warning" role="alert">
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
                          <option value="{{ $item->id }}"
                                  @if ($item->id == $user->company_id)
                                    selected
                                  @endif
                          >{{ $item->name }}</option>
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
                      <select class="form-control myselect2" name="branch_id" id="branch" required>
                        <option value="">---</option>
                        @foreach($branch as $item)
                          <option value="{{ $item->id }}"
                                  @if ($item->id == $user->branch_id)
                                    selected
                                  @endif
                            >{{ $item->name_km }}({{ $item->short_name }})</option>
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
                      <select class="form-control myselect2" name="department_id" id="department"
                        @if($user->branch_id <= 1) required @endif
                      >
                        <option value="">---</option>
                        @foreach($department as $item)
                          <option value="{{ $item->id }}"
                                  @if ($item->id == $user->department_id)
                                    selected
                                  @endif
                            >{{ $item->name_km }}</option>
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
                      <select class="form-control position-select2" name="position_id" required style="padding: 0.2rem .75rem;">
                        <option value="">---</option>
                        @foreach($positions as $item)
                          <option
                                  value="{{ $item->id }}"
                                  @if ($item->id == $user->position_id)
                                    selected
                                  @endif
                          >
                            {{ $item->name_km }}
                          </option>
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
                              value="{{ old('name', $user->name) }}"
                              required="true"
                              aria-required="true"
                              @if(!@admin_action()) readonly @endif
                      />
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('អ្នកប្រើប្រាស់') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                              name="username"
                              id="input-username"
                              type="text"
                              placeholder="{{ __('Username') }}"
                              value="{{ old('username', $user->username) }}"
                              required="true"
                              aria-required="true"
                              @if(!@admin_action()) disabled @endif
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
                              id="input-username"
                              type="number"
                              placeholder="{{ __('System ID') }}"
                              value="{{ old('system_user_id', $user->system_user_id) }}"
                              @if(!@admin_action()) disabled @endif
                      />
                      @if ($errors->has('system_user_id'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('system_user_id') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('អ៊ីម៉ែល') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input
                              class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                              name="email"
                              id="input-email"
                              type="email"
                              placeholder="yourname@sahakrinpheap.com.kh"
                              value="{{ old('email', $user->email) }}"
                      />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ភេទ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
                      <select class="form-control" name="gender" required>
                        <option value="">---</option>
                        <option value="M"
                                @if ($user->gender=='M')
                                  selected
                                @endif
                        >Male</option>
                        <option value="F"
                                @if ($user->gender=='F')
                                  selected
                                @endif
                        >Female</option>
                      </select>
                      @if ($errors->has('gender'))
                        <span id="name-gender" class="error text-danger" for="input-name">{{ $errors->first('gender') }}</span>
                      @endif
                    </div>
                  </div>
                </div>


                <!-- 1 = super admin, 2 = admin -->
                @if(@admin_action())
                    <div class="row">
                      <label class="col-sm-2 col-form-label">{{ __('ស្ថានភាព') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-7">
                        <div class="form-group{{ $errors->has('user_status') ? ' has-danger' : '' }}">
                          <select
                            class="form-control"
                            name="user_status"
                            @if($user->id == auth()->user()->id) disabled @endif
                          >
                            <option value="1">Active</option>
                            <option value="0"
                              @if ($user->user_status=='0')
                                selected
                              @endif
                            >Inactive</option>
                          </select>
                          @if ($errors->has('name'))
                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('user_status') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label" for="input-password">{{ __(' លេខសម្ងាត់') }}</label>
                      <div class="col-sm-7">
                        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                          <input
                            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                            type="password"
                            name="password"
                            id="input-password"
                            placeholder="{{ __('Password') }}"
                            value=""

                          />
                          @if ($errors->has('password'))
                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('password') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label" for="input-password-confirmation">{{ __('បញ្ជាក់លេខសម្ងាត់') }}</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <input
                            class="form-control"
                            name="password_confirmation"
                            id="input-password-confirmation"
                            type="password"
                            placeholder="{{ __('Confirm Password') }}"
                            value=""
                          />
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label">
                        Role
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="កំណត់សិទ្ធដែល User អាចធ្វើបាន"
                           data-placement="right"></i>
                      </label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="form-control" name="role">
                            <option value="">---</option>
                            <option
                              value="{{ config('app.system_admin_role') }}"
                              @if(config('app.system_admin_role') == $user->role) selected @endif
                              @if(config('app.system_admin_role') != auth()->user()->role) disabled @endif
                            >
                              Super Admin (Manage all and can see all request)
                            </option>
                            <option
                              value="{{ config('app.system_sub_admin_role') }}"
                              @if(config('app.system_sub_admin_role') == $user->role) selected @endif
                            >
                              Admin (Manage all and can't see all request)
                            </option>
                            <option
                              value="{{ config('app.system_manager_role') }}"
                              @if(config('app.system_manager_role') == $user->role) selected @endif
                            >
                              Manager (Can see summary report)
                            </option>
                            <option
                              value="{{ config('app.system_user_role') }}"
                              @if(config('app.system_user_role') == $user->role) selected @endif
                            >
                              User (Request only)
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <?php
                      $manage_template_report = is_array($user->manage_template_report) ? $user->manage_template_report : json_decode($user->manage_template_report);
                      $edit_pending_request = is_array($user->edit_pending_request) ? $user->edit_pending_request : json_decode($user->edit_pending_request);
                      $view_approved_request = is_array($user->view_approved_request) ? $user->view_approved_request : json_decode($user->view_approved_request);
                    ?>

                    <div class="row">
                      <label class="col-sm-2 col-form-label">
                        View Approved (request)
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="កំណត់ប្រភេទ Request ដែល User អាច View នៅពេលដែល Request បាន Approved"
                           data-placement="top"></i>
                      </label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="form-control myselect2" name="view_approved_request[]" multiple>
                            @foreach($request_type as $key => $value)
                              <option
                                value="{{ $value->id }}"
                                @if(@in_array( $value->id, @$view_approved_request ))
                                  selected
                                @endif
                              >
                                    {{ $value->name }} - {{ $value->name_km }}
                                </option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label">
                        Edit pending (request)
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="កំណត់ប្រភេទ Request ដែល User អាច Edit មុនពេល Request មិនទាន់ Approved"
                           data-placement="top"></i>
                      </label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="form-control myselect2" name="edit_pending_request[]" multiple>
                            @foreach($request_type as $key => $value)
                              <option
                                value="{{ $value->id }}"
                                @if(@in_array( $value->id, @$edit_pending_request ))
                                  selected
                                @endif
                              >
                                {{ $value->name }} - {{ $value->name_km }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label">
                        Manage template (report)
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="កំណត់ក្រុមហ៊ុន ដែល User អាច Manage ទៅលើ Template report"
                           data-placement="top"></i>
                      </label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="form-control myselect2" name="manage_template_report[]" multiple>
                            <option value="">---</option>
                            @foreach($companies as $item)
                              <option
                                value="{{ $item->id }}"
                                @if(@in_array( $item->id, @$manage_template_report ))
                                  selected
                                @endif
                              >{{ $item->name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label">
                        និរាករណ៍ Memo
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="Manage"
                           data-placement="top"></i>
                      </label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <input type="checkbox"
                            @if(@$user->action_object->can_abrogation) checked @endif
                            name="action_object[can_abrogation]"
                          > Active Abrogation<br>
                          <input type="checkbox"
                            @if(@$user->action_object->can_deabrogation) checked @endif
                            name="action_object[can_deabrogation]"
                          > Inactive Abrogation
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2 col-form-label" for="">{{ __('ហត្ថលេខា') }}</label>
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
                      <label class="col-sm-2 col-form-label" for="">{{ __('ហត្ថលេខាតូច') }}</label>
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
                      <label class="col-sm-2 col-form-label" for="">{{ __('រូបតំណាង') }}</label>
                      <div class="col-sm-7">
                        <div class="form-group form-file-upload">
                          <input name="avatar" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                        </div>
                      </div>
                      @if ($errors->has('avatar'))
                        <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('avatar') }}</span>
                      @endif
                    </div>
                @endif
                <div class="card" style="padding: 5px 1pc;">
                    <h5><strong for="">Contract Notification</strong></h5>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">{{ __('ប្រភេទមុខងារ') }}</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="role_type">

                                  <option value=""> << Please seelect >> </option>
                                  @foreach (Constants::CONTRACT_MANAGEMEMNT_ROLES as $key => $value)
                                    <option value="{{ $key }}" @if ($user->role_type == $key)selected @endif> {{ $value }} </option>
                                  @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">{{ __('TelegrameId') }}</label>
                        <div class="col-sm-7">
                          <div class="form-group{{ $errors->has('telegrame_id') ? ' has-danger' : '' }}">
                            <?php
                                $telegrame_obj = json_decode(@$notification->data);
                                $telegram_id = @$telegrame_obj->is_channel_telegram;
                            ?>
                            <input
                                    class="form-control{{ $errors->has('telegrame_id') ? ' is-invalid' : '' }}"
                                    name="telegrame_id"
                                    id="input-telegrame_id"
                                    type="text"
                                    placeholder=""
                                    value="{{ old('telegrame_id', @$telegram_id) }}"
                            />
                            @if ($errors->has('telegrame_id'))
                              <span id="telegrame_id-error" class="error text-danger" for="input-telegrame_id">{{ $errors->first('telegrame_id') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="col-sm-2">
                            <a  class="btn btn-info" @click="sendNotifacation()" data-original-title="" title="Send Notification">
                                Send Notification
                            </a>
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

@include('users.modal_edit')

@endsection
@push('js')
  <script>
    $(document).ready(function () {
      $(".position-select2").select2({
          tags: true
      });
      $(".myselect2").select2({

      });

      @if(! auth()->user()->email)
        $('#modal_edit').modal('show');
      @endif

      $('#branch_modal').on('change', function () {

        var branch_modal = $(this).val();
        if( branch_modal > 1 ){
          $('#department_modal').removeAttr('required');
        }
        else{
          $('#department_modal').attr('required', true);
        }

      });

      $('#branch').on('change', function () {

        var branch = $(this).val();
        if( branch > 1 ){
          $('#department').removeAttr('required').trigger('change');
        }
        else{
          $('#department').attr('required', true).trigger('change');
        }

      });

    });
    function sendNotifacation(){
      var teleg_id =  $('input[name="telegrame_id"]').val();
      $.ajax({
            type:'get',
            url:"{{ route('user.send.notification') }}",
            data: {teleg_id:teleg_id},
            success:function(data){
                // $('#showpayment table tbody').html(data);
            }
        });
    }
  </script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>

  <script>

      @if(session('status')==1)
          Swal.fire({
            title: 'Insert Success',
            icon: 'success',
            timer: '5000',
          })
      @elseif(session('status')==2)
          Swal.fire({
            title: 'Update Success',
            icon: 'success',
            timer: '5000',
          })
      @elseif(session('status')==3)
          Swal.fire({
            title: 'Delete Success',
            icon: 'success',
            timer: '5000',
          })
      @elseif(session('status')==4)
          Swal.fire({
            title: 'Please Try agian',
            icon: 'error',
            timer: '5000',
          })
      @endif
  </script>
@endpush
