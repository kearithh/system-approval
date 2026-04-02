@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 text-right">
          <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
        </div>
        <div class="col-sm-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('resign.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កែប្រែលិខិតលាឈប់ពីការងារ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control company select2" name="company_id" id="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id}}"
                                @if($data->company_id == $value->id))
                                  selected
                                @endif
                        >{{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>ប្រភេទសំណើរ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" name="type" id="type" required >
                      <option value=""><< ជ្រើសរើស >></option>
                      <option value="1"
                              @if($data->types == '1')
                                selected
                              @endif
                      >លិខិតលាឈប់ពីការងារបុគ្គលិក</option>
                      <option value="2"
                              @if($data->types == '2')
                                selected
                              @endif
                      >សំណើអនុញ្ញាតលាឈប់ពីការងារបុគ្គលិក</option>
                      <!-- <option value="3"
                              @if($data->types == '3')
                                selected
                              @endif
                      >លិខិតអនុញ្ញាតឱ្យឈប់ជាផ្លូវការបុគ្គលិក</option> -->
                    </select>
                    <input type="hidden" value="{{$data->title}}" name="title" id="title">
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label><span id="title_staff">{{$data->title}}ឈ្មោះ</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select_tags" name="staff" id="staff" required >
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($staffs as $key => $item)
                        <option
                            value="{{ $item->id }}"
                            data-position="{{$item->position_id}}"
                            data-department="{{$item->department_id}}"
                            data-branch="{{$item->branch_id}}"

                            @if($data->staff_id == $item->id)
                              selected
                            @endif
                        >{{ $item->staff_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ភេទ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="gender" id="gender" required >
                      <option value=""><< ជ្រើសរើស >></option>
                      <option value="M" @if ($data->gender == 'M') selected @endif >ប្រុស</option>
                      <option value="F" @if ($data->gender == 'F') selected @endif >ស្រី</option>
                    </select>
                  </div>
                </div>

                <div class="request_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">កាតបុគ្គលិកលេខ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              required 
                              type="text"
                              class="form-control request_resign"
                              name="card_id"
                              value="{{ $data->card_id }}" 
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="request_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">ថ្ងៃចូលបម្រើការងារ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              required 
                              type="text"
                              class="datepicker form-control request_resign"
                              name="doe"
                              data-inputmask-inputformat="dd-mm-yyyy"
                              placeholder="dd-mm-yyyy"
                              autocomplete="off"
                              value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('d-m-Y'))}}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="request_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">កុងត្រា<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <select class="form-control select2 request_resign" required name="is_contract" id="is_contract">
                          <option value=""><< ជ្រើសរើស >></option>
                          <option value="1" @if(@$data->is_contract == "1") selected @endif > ចប់កុងត្រា </option>
                          <option value="2" @if(@$data->is_contract == "2") selected @endif > មិនទាន់ចប់កុងត្រា </option>
                          <option value="3" @if(@$data->is_contract == "3") selected @endif > សាកល្បងការងារ </option>
                          
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                @if(@$data->is_contract == "2")
                  <div class="no_contract request_resign_show" style="display: block;">
                    <div class="row">
                      <div class="col-md-2">
                        <label>យល់ព្រមសងសំណងតាមកិច្ចសន្យាចំនួន (ខែ)<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <input type="text" class="form-control request_resign" value="{{ @$data->contract }}" id="contract" name="contract">
                      </div>
                    </div>
                  </div>
                @else
                  <div class="no_contract request_resign_show" style="display: none">
                    <div class="row">
                      <div class="col-md-2">
                        <label>យល់ព្រមសងសំណងតាមកិច្ចសន្យាចំនួន (ខែ)<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <input type="text" class="form-control request_resign" id="contract" name="contract">
                      </div>
                    </div>
                  </div>
                @endif

                <div class="row">
                  <div class="col-sm-2">
                    <label><span id="posi">តួនាទី / មុខងារ</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" name="position" id="position" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($position as $key => $value)
                        <option value="{{ $value->id }}" @if ($data->position == $value->id) selected @endif >
                          {{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>ការិយាល័យកណ្តាល / សាខា<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control company select2" name="branch" id="branch" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                        <option value="{{ $value->id }}" @if ($data->branch == $value->id) selected @endif >
                          {{ $value->name_km }} ({{ $value->short_name }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2 department">
                    <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group department">
                    <select class="form-control select2" name="department" id="department" required>
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($department as $key => $value)
                        <option value="{{ $value->id }}" @if ($data->department == $value->id) selected @endif >
                          {{ $value->name_km }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="request_resign_show">
                  <div class="row">
                    <div class="col-sm-2">
                      <label>បរិយាយមូលហេតុ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-sm-10 form-group">
                      <textarea class="form-control request_resign" required rows="3" id="reason" name="reason">{{$data->reason}}</textarea>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show">
                  <div class="row">
                    <div class="col-md-2">
                      <label>តំណភ្ជាប់លិខិតលាឈប់<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2 approver_resign" name="resign_id" id="resign_id">
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach($resign as $key => $value)
                          <option value="{{ $value->id }}" @if ($data->resign_id == $value->id) selected @endif >
                            {{ $value->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">លិខិតលាឈប់កាលពីថ្ងៃទី<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input  
                              type="text"
                              class="datepicker form-control approver_resign"
                              name="resign_object[resign_date]"
                              data-inputmask-inputformat="dd-mm-yyyy"
                              placeholder="dd-mm-yyyy"
                              autocomplete="off"
                              value="{{ @$data->resign_object->resign_date }}" 
                        >
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="approver_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">របាយការណ៌ផ្ទេរការងារចប់កាលពីថ្ងៃទី<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="datepicker form-control approver_resign"
                              name="resign_object[report_date]"
                              data-inputmask-inputformat="dd-mm-yyyy"
                              placeholder="dd-mm-yyyy"
                              autocomplete="off"
                              value="{{ @$data->resign_object->report_date }}" 
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">ទឹកប្រាក់សកម្ម<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[active_amount]"
                              value="{{ @$data->resign_object->active_amount }}" 
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">អតិថិជនចំនួន<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[number_customer]"
                              value="{{ @$data->resign_object->number_customer }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">អតិថិជនយឺតចំនួន<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[late_customer]"
                              value="{{ @$data->resign_object->late_customer }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">ទឹកប្រាក់សកម្មយឺតយ៉ាវ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[late_amount]"
                              value="{{ @$data->resign_object->late_amount }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">ឯកសារឥណទានរបស់អតិថិជនដែលខ្វះ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[customer_credit_document]"
                              value="{{ @$data->resign_object->customer_credit_document }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show_obj">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">ទ្រព្យធានាអតិថិជនដែលខ្វះ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign_obj"
                              name="resign_object[collateral_missing_customer]"
                              value="{{ @$data->resign_object->collateral_missing_customer }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="approver_resign_show">
                  <div class="row">
                    <label class="col-sm-2 col-form-label">
                      ប្រគល់ជូន
                      <i class="fa fa-xs fa-question-circle tooltipsign" 
                         data-toggle="tooltip"
                         title="អ្នកទទួល ឬទំនួស"
                         data-placement="top"></i>
                      <span style='color: red'>*</span>
                    </label>
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input 
                              type="text"
                              class="form-control approver_resign"
                              name="resign_object[handed]"
                              value="{{ @$data->resign_object->handed }}"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                      <div id="validate"></div>
                      <input
                          type="file"
                          id="file"
                          name="file"
                          value="{{ old('file') }}"
                      >
                      &emsp;&emsp;
                      @if(@$data->attachment)
                        <a href="{{ asset('/'.@$data->attachment) }}" target="_self">View old File</a>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>រៀបចំដោយ</label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($staffs as $item)
                        @if($item->id == $data->user_id)
                          <option value="{{ $item->id}}" selected="selected">{{ $item->staff_name }}</option>
                        @endif
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" name="review_by[]" required multiple>

                      @foreach($data->reviewers() as $item)
                        <option value="{{ $item->id }}" selected="selected">
                          {{ $item->name }}({{$item->position_name}})
                        </option>
                      @endforeach

                      @foreach($reviewer as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                      @endforeach

                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-2">
                    <label>
                      ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                      <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                         title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ MMI"
                         data-placement="top"></i>
                    </label>
                  </div>
                  <div class="col-sm-10 form-group">
                    <select class="form-control select2" name="review_short[]" multiple>

                      @foreach($data->reviewer_shorts() as $item)
                        <option value="{{ $item->id }}" selected="selected">
                          {{ $item->name }}({{ $item->position_name }})
                        </option>
                      @endforeach

                      @foreach($reviewer_short as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                      @endforeach

                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="approve_by" required >
                      @foreach($approver as $item)
                          <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                            {{ @$item->name }} ({{$item->position_name}})
                          </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                @if ($data->status == config('app.approve_status_reject'))
                  <button
                      @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
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
                      @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
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

@include('resign.partials.js')
