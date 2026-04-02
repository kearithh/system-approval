@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request_hr.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@push('css')
    <style>
        .table td {
            padding: 0.1em;
        }
    </style>
@endpush

@section('content')
    
    @include('global.style_default_approve')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើចំណាយទូទៅ | General Expense') }}</h4>
                <p class="card-category"></p>
              </div>

              <div class="card-body" style="margin-bottom: -45px">
                  <a href="{{ asset('template/General_Expense.xlsx') }}">
                    <i class="fas fa-file-excel"></i>
                    Template General Expense
                  </a>
                  <form id="importForm"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('request_hr.import') }}"
                        class="form-horizontal">
                      @csrf
                          <div class="row">
                            <div class="col-sm-6">
                              
                              <div class="input-group mb-3">

                                <div class="custom-file">
                                  <input type="file" name="file_import" accept="xls, .xlsx" required class="custom-file-input" id="customFile">
                                  <label class="custom-file-label" id="customFileLabel" for="customFile">Choose file</label>
                                </div>

                                <div class="input-group-append">
                                  <button class="btn btn-info" type="submit" name="Import" title="import only Excel file" id="button-addon2">
                                    <i class="fas fa-download"></i>
                                    Import
                                </button>
                                </div>
                              </div>

                            </div>
                          </div>
                  </form>
              </div>

              <form
                    id="requestForm"
                    method="POST"
                    enctype="multipart/form-data"
                    action="{{ route('request_hr.store') }}"
                    class="form-horizontal">
                @csrf
                @method('post')

              <div class="card-body">

                <div class="row">
                    
                  <div class="col-md-12">
                    <small><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></small>
                        @include('request_hr.item_table')
                   </div>
                    <div class="col-sm-12 mb-1">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ឯកសារភ្ជាប់ | Attachments</label>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                    <input
                                        type="file"
                                        id="file"
                                        class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                        name="file"
                                        value="{{ old('file') }}"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-1">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">{{ __('កំណត់សម្គាល់ | Notes') }}</label>
                            <div class="col-sm-9">
                                <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                                    <textarea
                                            id="remark"
                                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                                            name="remark"
                                    ></textarea>
                                    @if ($errors->has('remark'))
                                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-1">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">ទីតាំងការងារ | Location</label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input type="text" name="location" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-1">
                        <div class="row">
                            <div class="col-md-3">
                              <label>ក្រុមហ៊ុន | Company<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                              <select class="form-control company select2" name="company_id">
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
                    </div>
                    <div class="col-sm-12 mb-1">
                        <div class="row">
                            <div class="col-md-3">
                                <label>រៀបចំស្នើសុំ | Requestor<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" readonly="true" name="created_by" required>
                                    <option value="{{ @Auth::id() }}">{{ @Auth::user()->name }}-{{ @Auth::user()->name_en }}</option>
                                </select><br/>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
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
                                    <input type="hidden" name="" id="type_request" value="{{ config('app.type_general_expense') }}">
                                    <input type="hidden" name="" id="type_report" value="">
                                </div>
                            </legend>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        យល់ព្រម | Initial approved (ហត្ថលេខាតូច | Small Signature)1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាអ្នកគ្រប់គ្រងផ្ទាល់"
                                           data-placement="top"></i>
                                         <span style='color: red'>*</span> 
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2"  name="agree_by">
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        យល់ព្រម | Initial approved (ហត្ថលេខាតូច | Small Signature)2
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="agree_by_short">
                                        <option value="0"><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        ត្រួតពិនិត្យ | Verification
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាមន្រ្តីហិរញ្ញវត្ថុ"
                                           data-placement="top"></i>
                                        <span style='color: red'>*</span></label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control select2"  name="reviewer">
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $key => $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select><br/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        អនុម័តដោយ | Final Approved 1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                           data-placement="top"></i>
                                        <span style='color: #ff0000'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2"  name="reviewer_short_1">
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 


                            {{--<div class="row">
                                <div class="col-md-3">
                                    <label>
                                        អនុម័តដោយ | Final Approved 1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                           data-placement="top"></i>
                                           <span style='color: #ff0000'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="reviewer_short_2">
                                        <option value="0"><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>--}}

                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        អនុម័តដោយ | Final Approved 2
                                        <span style='color: #ff0000'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" required name="approver" required>
                                        <option value=""> << ជ្រើសរើស | Select >> </option>
                                        @foreach($approver as $item)
                                            <option value="{{ @$item->id }}" >{{ @$item->name }}-{{ @$item->name_en }}({{ @$item->position_name }})</option>
                                        @endforeach
                                    </select><br/>
                                </div>
                            </div>

                        </fieldset>
                    </div>

                </div>
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
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@include('request_hr.add_more_js')

@include('global.js_default_approve')
