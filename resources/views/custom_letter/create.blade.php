@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')

  @if (@auth()->user()->branch->branch == 1)
      <h2 style="color: red"> User in Branch can't use</h2>
      <?= die() ?>
  @endif

  @include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('custom_letter.store') }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើរដ្ឋបាលទូទៅ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
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
                    <label>កម្មវត្ថុ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <input
                              type="text"
                              class="form-control"
                              name="purpose"
                              required
                        >
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>យោង<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                     <!--  <input
                              type="text"
                              class="form-control"
                              name="reference"
                              required
                        > -->
                      <textarea class="form-control point_textarea" required rows="3" name="reference"></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>បរិយាយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-10 form-group">
                    <textarea class="form-control point_textarea" required rows="3" name="description">សេចក្តីដូចបានជម្រាបជូនតាមកម្មវត្ថុ និងយោងខាងលើ ខ្ញុំបាទ/នាងខ្ញុំសូមជម្រាបជូន...</textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                      <input
                          type="file"
                          id="file"
                          name="file[]"
                          value="{{ old('file') }}"
                      >
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
                      By default
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
                      <label>
                        ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
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
                      <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
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
                      <label>ចម្លងជូន(CC)
                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                           title="ផ្នែកពាក់ព័ន្ធដែលជួយដឹងលឺ ជាទូទៅខាងផ្នែកហិរញ្ញវត្ថុ..."
                           data-placement="top"></i>
                      </label>
                    </div>  
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="cc[]" multiple="multiple">
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-2">
                      <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control js-approver" name="approve_by" required>
                        <option value=""><< ជ្រើសរើស >></option>
                        @foreach($approver as $item)
                          <option value="{{ @$item->id }}">
                            {{ @$item->name }}({{ $item->position_name }})
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
                  class="btn btn-success"
                >
                  Submit
                </button>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@include('custom_letter.partials.js')
@include('global.js_default_approve')
