@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')

  @include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('hr_request.store') }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើរធនធានមនុស្ស | HR Request') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន | For companies<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control company select2" id="company_id" name="company_id">
                      @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
                                @if(Auth::user()->company_id == $value->id))
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
                    <label>ប្រភេទសំណើរ | Request type<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select2" name="type" id="type" required >
                      <option value=""><< ជ្រើសរើស | Select >></option>
                      @foreach(config('app.letter_request') as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="title" id="title">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label><span id="title_staff">​​​​​​ បុគ្គលិកឈ្មោះ | Employee Name​ ​</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <select class="form-control select_tags" name="staff" id="staff" required >
                      <option value=""><< ជ្រើសរើស | Select >></option>
                      @foreach($staffs as $key => $item)
                        <option
                            value="{{ $item->id }}"
                            data-position="{{$item->position_id}}"
                            data-department="{{$item->department_id}}"
                            data-branch="{{$item->branch_id}}"
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
                            <option value="{{ $value->id}}">
                              {{ $value->name }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div> -->

                      <div class="col-md-4">
                        <label><span id="old_posi">តួនាទីបច្ចុប្បន្ន | Current role</span><span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" name="old_position" id="old_position" required>
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($position as $key => $value)
                            <option value="{{ $value->id }}">
                              {{ $value->name_km }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label>ការិយាល័យកណ្តាល / សាខា | Head Office / Branch<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control company select2" name="old_branch" id="old_branch" required>
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($branch as $key => $value)
                            <option value="{{ $value->id }}">
                              {{ $value->name_km }} ({{ $value->short_name }})
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4 old_department">
                        <label>នាយកដ្ឋាន | Department<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group old_department">
                        <select class="form-control select2" name="old_department" id="old_department">
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($department as $key => $value)
                            <option value="{{ $value->id }}">
                              {{ $value->name_km }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label id="old_salary">ប្រាក់បៀរវត្សរ៍គោល | Basic Salary<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <input type="text" class="form-control" name="old_salary">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- new -->
                    <div id="new" class="row">
                      <!-- <div class="col-md-4">
                        <label>ក្រុមហ៊ុនថ្មី<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" required name="new_company">
                          <option value=""><< ជ្រើសរើស >></option>
                          @foreach($company as $key => $value)
                            <option value="{{ $value->id}}">
                              {{ $value->name }}
                            </option>
                          @endforeach()
                        </select><br/>
                      </div> -->

                      <div class="col-md-4">
                        <label><span id="title_staff">តួនាទីថ្មី | New role</span><span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control select2" required name="new_position" id="new_position">
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($position as $key => $value)
                            <option value="{{ $value->id }}">
                              {{ $value->name_km }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label>ការិយាល័យកណ្តាល / សាខាថ្មី | Head Office / New Branch<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <select class="form-control company select2" required name="new_branch" id="new_branch">
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($branch as $key => $value)
                            <option value="{{ $value->id}}">
                              {{ $value->name_km }} ({{ $value->short_name }})
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4 new_department">
                        <label>នាយកដ្ឋានថ្មី | New Department</label>
                      </div>
                      <div class="col-md-8 form-group new_department">
                        <select class="form-control select2" name="new_department" id="new_department">
                          <option value=""><< ជ្រើសរើស | Select >></option>
                          @foreach($department as $key => $value)
                            <option value="{{ $value->id }}">
                              {{ $value->name_km }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label id="new_salary">ប្រាក់បៀរវត្សថ្មី | New salary<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-8 form-group">
                        <input type="text" class="form-control" name="new_salary">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- សំណើសុំផ្លាស់ប្ដូរម៉ោងការងារបុគ្គលិក -->
                <div class="row" id="time_schedule" hidden>
                  <label class="col-sm-2 col-form-label">ម៉ោងការងារចាស់ | Old working hours: <span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input type="text" class="form-control" name="old_timetable">
                    </div>
                  </div>
                  <label class="col-sm-2 col-form-label">ម៉ោងការងារថ្មី | New working hours<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                  <div class="form-group">
                      <input type="text" class="form-control" name="new_timetable">
                    </div>
                  </div>
                </div>

                <div class="row" id="working_day" hidden>
                  <div class="col-md-2">
                    <label><span id="working_date">ថ្ងៃធ្វើការ | Working days:</span><span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <input type="text" class="form-control" name="working_day">
                  </div>
                </div>
                <!-- បិទសំណើសុំផ្លាស់ប្ដូរម៉ោងការងារបុគ្គលិក -->
                <div class="row" id="time" hidden>
                  <label class="col-sm-2 col-form-label">ទឹកប្រាក់ម៉ោងត្រូវបានតម្លើង | Hourly rate increased<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input type="text" class="form-control" name="increase">
                    </div>
                  </div>

                  <label class="col-sm-2 col-form-label">ថ្ងៃចូលបម្រើការងារ | Date of entry<span style='color: red'>*</span></label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input
                            type="text"
                            class="datepicker form-control"
                            name="doe"
                            data-inputmask-inputformat="dd-mm-yyyy"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                      >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-2 col-form-label">មានប្រសិទ្ធិភាពចាប់ពី | Effective from<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group">
                      <input
                            type="text"
                            class="datepicker form-control"
                            name="effective"
                            required
                            value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}"
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
                    <textarea class="form-control point_textarea" required rows="3" id="reason" name="reason"></textarea>
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
                        @if($item->id==Auth::id())
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <input type="hidden" name="" id="my_department" value="{{ Auth::user()->department_id }}">
                  <input type="hidden" name="" id="my_type" value="request">
                  <input type="hidden" name="" id="type_request" value="{{ config('app.type_hr_request') }}">
                  <input type="hidden" name="" id="type_report" value="">
                </div>

                <fieldset>
                  <legend>
                    <button 
                          type="button"
                          value="1"
                          name="check"
                          class="check btn btn-sm btn-info">
                      By default in range
                    </button>
                    <button 
                          type="button"
                          value="2"
                          name="check"
                          class="check btn btn-sm btn-primary">
                      By default out range
                    </button>
                    <button
                          type="button"
                          value="1"
                          name="clear"
                          class="clear btn btn-sm btn-secondary">
                      Clear default
                    </button>
                  </legend>

                  <div class="row">
                    <div class="col-md-2">
                      <label>ត្រួតពិនិត្យដោយ | Reviewed by<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control js-reviewer-multi" name="review_by[]" required multiple>
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
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
                      <select class="form-control js-short-multi" name="review_short[]" multiple>
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>អនុម័តដោយ | Approved<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control js-approver" name="approve_by" required>
                        <option value=""><< ជ្រើសរើស | Select >></option>
                        @foreach($approver as $item)
                          <option value="{{ @$item->id }}">
                            {{ @$item->name }}-{{ @$item->name_en }}({{$item->position_name}})
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </fieldset>
              </div>

              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        class="btn btn-success">
                  {{ __('Submit') }}
                </button>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('hrRequest.partials.js')
@include('global.js_default_approve')
