@extends('adminlte::page', ['activePage' => 'setting-reviewer-approver', 'titlePage' => __('Create Position')])
@section('plugins.Select2', true)
@section('content')

@include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  method="post"
                  action="{{ route('setting-reviewer-approver.update', $setting) }}"
                  enctype="multipart/form-data"
                  autocomplete="off"
                  class="form-horizontal">
            @csrf
            @method('post')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Reviewer and Approver') }}</h4>
                <div class="text-right">
                  <a href="{{ route('setting-reviewer-approver.index') }}" class="btn btn-sm btn-success">{{ __('Back to list') }}</a>
                </div>
              </div>
              <div class="card-body ">

                <div class="row">
                    <div class="col-md-2">
                        <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="company" required>
                            @foreach($company as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if($setting->company_id == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="department" required>
                            @foreach($department as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if($setting->department_id == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>ប្រភេទ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" id="type" name="type" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            <option value="request" @if($setting->type == "request")) selected @endif > សំណើ </option>
                            <option value="report" @if($setting->type == "report")) selected @endif>របាយការណ៍</option>
                        </select>
                    </div>
                </div>

                <div class="row request">
                    <div class="col-md-2">
                        <label>ប្រភេទសំណើ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_request" name="type_request" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            @foreach($request_type as $key => $value)
                                <option value="{{ $value->id }}" @if($setting->type_request == $value->id) selected @endif >
                                    {{ $value->name }} - {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row report">
                    <div class="col-md-2">
                        <label>ប្រភេទរបាយការណ៍<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_report" name="type_report" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            @foreach($tags as $key => $value)
                                <option value="{{ $value->slug }}" @if($setting->type_report == $value->slug) selected @endif >
                                    {{ $value->name }} - {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row request">
                    <div class="col-md-2">
                        <label>ក្នុង / ក្រៅ
                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                title="ក្នុង=ក្នុងផែនការឬក្នុងRange, ក្រៅ=ក្រៅផែនការឬក្នុងRange"
                                data-placement="top"></i>
                         <span style='color: red'>*</span>
                        </label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2 type_request" name="category" required>
                            <option value=""><< ជ្រើសរើស >></option>
                            <option value="1" @if($setting->category == "1")) selected @endif >ក្នុង</option>
                            <option value="2" @if($setting->category == "2")) selected @endif >ក្រៅ</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកត្រួតពិនិត្យ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="reviewers[]" multiple required>
                            <?php $big = @\App\SettingReviewerApprover::reviewers(@$setting->reviewers) ?>
                            @if(@$big)
                                @foreach($big as $key => $value)
                                    <option value="{{ $value->id }}" selected>
                                        {{ $value->reviewer_name }}
                                    </option>
                                @endforeach
                            @endif

                            @foreach($reviewers as $key => $value)
                                <option value="{{ $value->id }}" @if(in_array($value->id, $setting->reviewers)) selected @endif>
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកត្រួតពិនិត្យ(ហត្ថលេខាតូច)</label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="reviewers_short[]" multiple>
                            <?php $short = @\App\SettingReviewerApprover::reviewers(@$setting->reviewers_short) ?>
                            @if(@$short)
                                @foreach($short as $key => $value)
                                    <option value="{{ $value->id }}" selected>
                                        {{ $value->reviewer_name }}
                                    </option>
                                @endforeach
                            @endif

                            @foreach($reviewers_short as $key => $value)
                                <option value="{{ $value->id }}">
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>កំណត់អ្នកអនុម័ត<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                        <select class="form-control select2" name="approver" required>
                            @foreach($staff as $key => $value)
                                <option value="{{ $value->id }}" @if($setting->approver == $value->id)) selected @endif>
                                    {{ $value->reviewer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <fieldset>
                    <legend>
                        Approver the request
                    </legend>
                    <div class="row">
                        <div class="col-md-2">
                            <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                            <select class="form-control select2" name="my_approver" required>
                                @foreach($staff as $key => $value)
                                    <option 
                                        value="{{ $value->id }}"
                                        @if($value->id == @$setting->approver()->id) selected @endif 
                                    >
                                        {{ $value->reviewer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

              </div>
              <div class="card-footer">
                  @if ($setting->status == config('app.approve_status_reject'))
                      <button
                          @if ($setting->user_id != \Illuminate\Support\Facades\Auth::id())
                              disabled
                              title="Only requester that able to edit the request"
                          @endif
                          type="submit"
                          value="1"
                          name="resubmit"
                          class="btn btn-info">
                          {{ __('Re-Submit') }}
                      </button>
                  @else
                      <button
                          @if ($setting->user_id != \Illuminate\Support\Facades\Auth::id())
                              disabled
                              title="Only requester that able to edit the request"
                          @endif
                          type="submit"
                          value="1"
                          name="submit"
                          class="btn btn-success">
                          {{ __('Update') }}
                      </button>
                  @endif
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('setting.partials.js')