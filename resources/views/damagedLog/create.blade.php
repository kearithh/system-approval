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
                  action="{{ route('damagedlog.store') }}"
                  enctype="multipart/form-data"
                  class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កំណត់ហេតុស្តីពីទ្រព្យសម្បត្តិខូចខាត ឬបាត់បង់') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  
                  @include('damagedLog.partials.item_table')
                  <br>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ឯកសារភ្ជាប់</label>
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
                      <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
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
                      <label>បរិយាយមូលហេតុ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="point_textarea form-control"
                          name="desc"
                          required
                      ></textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>សំណង<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="is_penalty" id="is_penalty">
                        <option value="0">មិនផ្តល់សំណង</option>
                        <option value="1">ផ្តល់សំណង</option>
                      </select>
                    </div>
                  </div>
                  <div class="penalty" style="display: none">
                    <div class="row">
                      <div class="col-md-2">
                        <label>ឈ្មោះអ្នកផ្តល់សំណង<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <input type="text" class="form-control" id="penalty_name" name="penalty[name]">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2">
                        <label>រូបិយប័ណ្ណ<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control select2" id="currency" name="penalty[currency]">
                          <option value="USD">ដុល្លារ</option>
                          <option value="KHR">រៀល</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2">
                        <label>ទឹកប្រាក់<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <input type="number" step="0.00001" id="amount" class="form-control" name="penalty[amount]">
                      </div>
                    </div>
                  </div>
                  <div class="no_penalty">
                    <div class="row">
                      <div class="col-md-2">
                        <label>ហេតុផល<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <textarea class="form-control" required rows="3" id="reason" name="penalty[reason]"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-2">
                      <label>រៀបចំដោយ</label>
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

                  <fieldset>
                    <legend>
                      <button 
                            type="button"
                            value="1"
                            name="check"
                            class="check btn btn-sm btn-info">
                        By default
                      </button>
                      <button
                            type="button"
                            value="1"
                            name="clear"
                            class="clear btn btn-sm btn-secondary">
                        Clear default
                      </button>
                      <div class="row">
                          <input type="hidden" name="" id="my_department" value="{{ Auth::user()->department_id }}">
                          <input type="hidden" name="" id="my_type" value="request">
                          <input type="hidden" name="" id="type_request" value="{{ config('app.type_damaged_log') }}">
                          <input type="hidden" name="" id="type_report" value="">
                      </div>
                    </legend>

                    <div class="row">
                      <div class="col-md-2">
                        <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
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
                        <label class="col-sm-2 col-form-label">
                          ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                          <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                               title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                               data-placement="top"></i>
                        </label>
                      <div class="col-sm-10 form-group">
                        <select class="form-control js-short-multi" name="review_short[]" multiple>
                          @foreach($reviewer as $item)
                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-2">
                        <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control approver js-approver" name="approver" required>
                          <option value=""> << ជ្រើសរើស >> </option>
                          @foreach($approver as $item)
                            <option value="{{ @$item->id }}">
                              {{ @$item->name }}({{$item->position_name}})
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

@include('damagedLog.partials.add_more_js')

@include('global.js_default_approve')
