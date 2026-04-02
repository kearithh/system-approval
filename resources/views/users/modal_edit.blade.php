
<div class="modal fade"  data-keyboard="false" data-backdrop="static" id="modal_edit" tabindex="-1" role="dialog" aria-labelledby="modal_edit">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">
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
                    <h4 class="card-title">{{ __('Update your profile') }}</h4>
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
                      <label class="col-sm-3 col-form-label">{{ __('ក្រុមហ៊ុន') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
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
                      <label class="col-sm-3 col-form-label">{{ __('ការិយាល័យកណ្តាល / សាខា') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('branch_id') ? ' has-danger' : '' }}">
                          <select class="form-control myselect2" name="branch_id" id="branch_modal" required>
                            <option value="">---</option>
                            @foreach($branch as $item)
                              <option value="{{ $item->id }}"
                                      @if ($item->id == $user->branch_id)
                                        selected
                                      @endif
                                >{{ $item->name_km }}</option>
                            @endforeach
                          </select>
                          @if ($errors->has('branch_id'))
                            <span id="name-branch_id" class="error text-danger" for="input-name">{{ $errors->first('branch_id') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-3 col-form-label">{{ __('នាយកដ្ឋាន') }}</label>
                      <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('department_id') ? ' has-danger' : '' }}">
                          <select class="form-control myselect2" name="department_id" id="department_modal" 
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
                      <label class="col-sm-3 col-form-label">{{ __('មុខតំណែង') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
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
    {{--                            {{ old('position_id') ? $positions[old('position_id')] : $item->name_km }}--}}
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
                      <label class="col-sm-3 col-form-label">{{ __('គោត្តនាម និងនាម(ខ្មែរ)') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
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
                          />
                          @if ($errors->has('name'))
                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-3 col-form-label">{{ __('អ្នកប្រើប្រាស់') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
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
                                  @if (!@admin_action()) disabled @endif
                          />
                          @if ($errors->has('username'))
                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('username') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-3 col-form-label">{{ __('អ៊ីម៉ែល') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                          <input
                                  class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                  name="email"
                                  id="input-email"
                                  type="email"
                                  placeholder="yourname@sahakrinpheap.com.kh"
                                  value="{{ old('email', $user->email) }}"
                                  required
                          />
                          @if ($errors->has('email'))
                            <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-3 col-form-label">{{ __('ភេទ') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-9">
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

                    @if (@admin_action())
                        <div class="row">
                          <label class="col-sm-3 col-form-label">{{ __('ស្ថានភាព') }}<span style='color: red'>*</span></label>
                          <div class="col-sm-9">
                            <div class="form-group{{ $errors->has('user_status') ? ' has-danger' : '' }}">
                              <select 
                                    class="form-control" 
                                    name="user_status"
                                    @if($user->id == auth()->user()->id) disabled @endif
                                    >
                                    <option value="1">Active</option>
                                    <option value="0" @if ($user->user_status=='0') selected @endif>Inactive</option>
                              </select>
                              @if ($errors->has('name'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('user_status') }}</span>
                              @endif
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <label class="col-sm-3 col-form-label" for="input-password">{{ __(' លេខសម្ងាត់') }}</label>
                          <div class="col-sm-9">
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
                          <label class="col-sm-3 col-form-label" for="input-password-confirmation">{{ __('បញ្ជាក់លេខសម្ងាត់') }}</label>
                          <div class="col-sm-9">
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
                          <label class="col-sm-3 col-form-label" for="">{{ __('ហត្ថលេខា') }}</label>
                          <div class="col-sm-9">
                            <div class="form-group form-file-upload {{ $errors->has('signature') ? ' has-danger' : '' }}">
                              <input name="signature" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                            </div>
                            @if ($errors->has('signature'))
                              <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('signature') }}</span>
                            @endif
                          </div>
                        </div>

                        <div class="row">
                          <label class="col-sm-3 col-form-label" for="">{{ __('ហត្ថលេខាតូច') }}</label>
                          <div class="col-sm-9">
                            <div class="form-group form-file-upload {{ $errors->has('short_signature') ? ' has-danger' : '' }}">
                              <input name="short_signature" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                            </div>
                            @if ($errors->has('short_signature'))
                              <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('short_signature') }}</span>
                            @endif
                          </div>
                        </div>

                        <div class="row">
                          <label class="col-sm-3 col-form-label" for="">{{ __('រូបតំណាង') }}</label>
                          <div class="col-sm-9">
                            <div class="form-group form-file-upload">
                              <input name="avatar" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                            </div>
                          </div>
                          @if ($errors->has('avatar'))
                            <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('avatar') }}</span>
                          @endif
                        </div>
                    @endif
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