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
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('hr_request.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កែប្រែសំណើរធនធានមនុស្ស | Edit HR Request') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន | For companies<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
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
                  <div class="col-md-2">
                    <label>ប្រភេទសំណើរ | Request type<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="type" id="type" required >
                      <option value=""><< ជ្រើសរើស | Select >></option>
                      @foreach(config('app.letter_request') as $key => $value)
                        <option value="{{ $key }}" @if ($data->types == $key) selected @endif >
                          {{ $value }}
                        </option>
                      @endforeach
                    </select>
                    <input type="hidden" value="{{$data->title}}" name="title" id="title">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label><span id="title_staff">{{ $data->title }}ឈ្មោះ | Employee Name</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select_tags" name="staff" id="staff" required >
                      <option value=""><< ជ្រើសរើស | Select>></option>
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
                  <div class="col-md-6">

                    <!-- old  -->
                    <div class="row">
                      <!-- <div class="col-md-4">
                        <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" required name="old_company">
                          <option value=""><< ជ្រើសរើស >></option>
                          @foreach($company as $key => $value)
                            <option value="{{ $value->id}}"
                                    @if($data->old_company == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div> -->

                      <div class="col-md-4">
                        <label>
                          <span id="old_posi">
                            @if($data->types == '3')
                              តួនាទីជា | position as
                            @else
                              តួនាទីបច្ចុប្បន្ន | Current position
                            @endif
                          </span>
                          <span style='color: red'>*</span>
                        </label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" id="old_position" name="old_position" required>
                          <option value=""><< ជ្រើសរើស | select >></option>
                          @foreach($position as $key => $value)
                            <option value="{{ $value->id}}"
                                    @if($data->old_position == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name_km }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div>

                      <div class="col-md-4">
                        <label>ការិយាល័យកណ្តាល / សាខា | Head Office / Branch<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control company select2" id="old_branch" name="old_branch" required>
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($branch as $key => $value)
                            <option value="{{ $value->id }}"
                                    @if($data->old_branch == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name_km }} ({{ $value->short_name }})
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4 old_department" @if($data->old_branch != 1) hidden @endif>
                        <label>នាយកដ្ឋាន | Department<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group old_department" @if($data->old_branch != 1) hidden @endif>
                        <select class="form-control select2"
                                id="old_department"
                                name="old_department"
                        >
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($department as $key => $value)
                            <option value="{{ $value->id }}"
                                    @if($data->old_department == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name_km }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div>

                      <div class="col-md-4">
                        <label id="old_salary">
                          @if($data->types == 0)
                            ប្រាក់ម៉ោងបច្ចុប្បន្ន | Current Hours
                          @else
                            ប្រាក់បៀរវត្សរ៍គោល | Base Salary
                          @endif
                          <span style='color: red'>*</span>
                        </label>
                      </div>
                      <div class="col-md-8 form-group">
                        <input type="text" value="{{ $data->old_salary }}" class="form-control" name="old_salary">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- new -->
                    <div class="row" id="new" @if($data->types == 3) hidden @endif>
                      <!-- <div class="col-md-4">
                        <label>ក្រុមហ៊ុនថ្មី<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" required name="new_company">
                          <option value=""><< ជ្រើសរើស >></option>
                          @foreach($company as $key => $value)
                            <option value="{{ $value->id}}"
                                    @if($data->new_company == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div> -->
                      <div class="col-md-4">
                        <label>តួនាទីថ្មី | New Position<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2"
                                id="new_position"
                                name="new_position"
                                @if($data->types != 3 && $data->types !=6) required @endif
                        >
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($position as $key => $value)
                            <option value="{{ $value->id}}"
                                    @if($data->new_position == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name_km }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div>

                      <div class="col-md-4">
                        <label>ការិយាល័យកណ្តាល / សាខាថ្មី | Head Office / New Branch<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control company select2"
                                name="new_branch"
                                id="new_branch"
                                @if($data->types != 3 && $data->types !=6) required @endif
                        >
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($branch as $key => $value)
                            <option value="{{ $value->id }}"
                                    @if($data->new_branch == $value->id) selected @endif
                            >
                              {{ $value->name_km }} ({{ $value->short_name }})
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4 new_department" @if($data->new_branch != 1) hidden @endif>
                        <label>នាយកដ្ឋានថ្មី | New Department<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group new_department" @if($data->new_branch != 1) hidden @endif>
                        <select class="form-control select2"
                                name="new_department"
                                id="new_department"
                        >
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($department as $key => $value)
                            <option value="{{ $value->id}}"
                                    @if($data->new_department == $value->id)
                                      selected
                                    @endif
                            >
                              {{ $value->name_km }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div>

                      <div class="col-md-4">
                        <label id="old_salary">
                          @if($data->types == 0)  
                            ប្រាក់ម៉ោងថ្មី | New hourly pay
                          @else
                            ប្រាក់បៀរវត្សថ្មី | New Salary
                          @endif
                          <span style='color: red'>*</span>
                        </label>
                      </div>
                      <div class="col-md-8 form-group">
                        <input type="text" value="{{ $data->new_salary }}" class="form-control" name="new_salary">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" id="time" @if($data->types != 0) hidden @endif>
                  <label class="col-sm-2 col-form-label">ទឹកប្រាក់ម៉ោងត្រូវបានតម្លើង | Hourly rate increased<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="{{$data->increase}}" name="increase">
                    </div>
                  </div>

                  <label class="col-sm-2 col-form-label">ថ្ងៃចូលបម្រើការងារ | Date of entry<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input
                            type="text"
                            class="datepicker form-control"
                            name="doe"
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($data->doe))->format('d-m-Y'))}}"
                            data-inputmask-inputformat="dd-mm-yyyy"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                      >
                    </div>
                  </div>
                </div>

                <div class="row" id="time_schedule" @if($data->types != 6) hidden @endif>
                  <label class="col-sm-2 col-form-label">ម៉ោងធ្វើការចាស់ | Old working hours<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="{{$data->old_timetable}}" name="old_timetable">
                    </div>
                  </div>

                  <label class="col-sm-2 col-form-label">ម៉ោងធ្វើការថ្មី | New working hours<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                  <div class="form-group">
                      <input type="text" class="form-control" value="{{$data->new_timetable}}" name="new_timetable">
                    </div>
                  </div>
                </div>
                <div class="row" id="working_day" @if($data->types != 6) hidden @endif>
                  <label class="col-sm-2 col-form-label">ថ្ងៃធ្វើការ | Working days<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group">
                      <input type="text" class="form-control" value="{{$data->working_day}}" name="working_day">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">មានប្រសិទ្ធិភាពចាប់ពី | Effective from<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('title_en') ? ' has-danger' : '' }}">
                      <input
                            type="text"
                            class="datepicker form-control"
                            name="effective"
                            required
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($data->effective_date))->format('d-m-Y'))}}"
                            data-inputmask-inputformat="dd-mm-yyyy"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                      >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>បរិយាយមូលហេតុ | Explain the reason<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <textarea class="form-control point_textarea" required rows="3" id="reason" name="reason">{!! $data->reason !!}</textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់ | Attachments</label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
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
                  <div class="col-md-2">
                    <label>រៀបចំដោយ | Prepared by</label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($reviewer as $item)
                        @if($item->id == $data->user_id)
                          <option value="{{ $item->id }}" selected="selected">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ត្រួតពិនិត្យដោយ | Reviewed by<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="review_by[]" required multiple>

                      @foreach($data->reviewers() as $item)
                        <option value="{{ $item->id }}" selected="selected">
                          {{ $item->name }}({{ $item->position_name }})
                        </option>
                      @endforeach

                      @foreach($reviewer as $key => $value)
                        <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                      @endforeach

                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>
                      ត្រួតពិនិត្យ(ហត្ថលេខាតូច) | Check (small signature)
                      <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                           data-placement="top"></i>
                    </label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="review_short[]" multiple>

                      @foreach($data->reviewers_short() as $item)
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
                    <label>អនុម័តដោយ | Approved<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="approve_by" required >
                      @foreach($approver as $item)
                          <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                            {{ @$item->name }}-{{ @$item->name_en }}({{ $item->position_name }})
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

@include('hrRequest.partials.js')
