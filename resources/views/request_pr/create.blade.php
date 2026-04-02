@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request_pr.index') }}
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
          <form
                  id="requestPR"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('request_pr.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestPR->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើបញ្ជាទិញ/Purchase Request​​') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  @include('request_pr.add_more_item_table')
                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
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
                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                          <select class="form-control select2" id="company_id" name="company_id">
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
                            @if ($errors->has('company_id'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('company_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('ស្នើដោយ') }}</label>
                  <div class="col-sm-9">
                    <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                      <select required class="form-control select2 request-by-select2" name="user_id">
                        @foreach($requester as $item)
                          @if($item->id==Auth::id())
                              <option value="{{ $item->id}} " selected="selected">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
                          @endif
                        @endforeach
                      </select>
                      @if ($errors->has('user_id'))
                        <span
                                id="name-error"
                                class="error text-danger"
                                for="input-name">
                          {{ $errors->first('user_id') }}
                        </span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                                    <div class="col-md-3">
                                        <label>នាយកដ្ឋាន </label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control reviewer my_select" name="department">
                                            <option value=""> << Select >> </option>
                                            @foreach($department as $key => $value)
                                                <option value="{{ $value->id }}">
                                                  {{ $value->name_en }}
                                                </option>
                                            @endforeach()
                                        </select>
                                    </div>
                                </div>

                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('Reason for purchase/Project detail:') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-9">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              id="purpose"
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"

                      >@if($requestPR){{ $requestPR->purpose }}@else{{ old('purpose') }}@endif</textarea>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('Vendor') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                    <textarea
                            id="reason"
                            class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                            name="reason"
                    >@if($requestPR){{ $requestPR->reason }}@else{{ old('reason') }}@endif</textarea>
                            @if ($errors->has('reason'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('Email/Phone') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                    <textarea
                            id="remark"
                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                            name="remark"
                    >@if($requestPR){{ $requestPR->remark }}@else{{ old('remark') }}@endif</textarea>
                            @if ($errors->has('remark'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('FOR') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('remarks') ? ' has-danger' : '' }}">
                    <textarea
                            id="remarks"
                            class="form-control{{ $errors->has('remarks') ? ' is-invalid' : '' }}"
                            name="remarks"
                    >@if($requestPR){{ $requestPR->remarks }}@else{{ old('remarks') }}@endif</textarea>
                            @if ($errors->has('remarks'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remarks') }}</span>
                            @endif
                        </div>
                    </div>
                </div>





    <div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Sourcing requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="sourcing_requirement" name="sourcing_requirement_yes" value="1" @if($data->sourcing_requirement_yes) checked @endif>
            <label for="sourcing_requirement">Yes</label><br>
            <input type="hidden" name="sourcing_requirement_no" value="0">
            <input type="checkbox" id="sourcing_requirement_no" name="sourcing_requirement_no" value="0" @if(!$data->sourcing_requirement_no) checked @endif>
            <label for="sourcing_requirement_no">No</label>
        </div>
    </div>
</div>

<div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Prefer supplier/ Single supplier Requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="prefer_supplier_yes" name="prefer_supplier_yes" value="1" @if($data->prefer_supplier_yes) checked @endif>
            <label for="prefer_supplier_yes">Yes</label><br>
            <input type="hidden" name="prefer_supplier_no" value="0">
            <input type="checkbox" id="prefer_supplier_no" name="prefer_supplier_no" value="0" @if(!$data->prefer_supplier_no) checked @endif>
            <label for="prefer_supplier_no">No</label>
        </div>
    </div>
</div>

<div class="row">
    <label class="col-sm-3 col-form-label">{{ __('Tender Requirement') }}</label>
    <div class="col-sm-9">
        <div class="form-group">
            <input type="checkbox" id="tender_requirement_yes" name="tender_requirement_yes" value="1" @if($data->tender_requirement_yes) checked @endif>
            <label for="tender_requirement_yes">Yes</label><br>
            <input type="hidden" name="tender_requirement_no" value="0">
            <input type="checkbox" id="tender_requirement_no" name="tender_requirement_no" value="0" @if(!$data->tender_requirement_no) checked @endif>
            <label for="tender_requirement_no">No</label>
        </div>
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
                            <input type="hidden" name="" id="my_type" value="request_pr">
                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_pr_request') }}">
                            <input type="hidden" name="" id="type_report" value="">
                        </div>
                    </legend>
                    {{-- <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        ស្នើរដោយ | Requestor by
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាអ្នកគ្រប់គ្រងផ្ទាល់"
                                           data-placement="top"></i>
                                         <span style='color: red'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" required name="agree_by">
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                       អនុម័តដំបូង | Initial Approval 1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="verify_by_1">
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
                                        អនុម័តដោយ | Non Medical or Medical
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                       <span style='color: #ff0000'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="verify_by_2" required>
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            @if($item->id == 37)
                                                <option value="{{ $item->id }}"><strong>Non-Medical</strong> - {{ $item->reviewer_name }}</option>
                                            @endif
                                            {{-- @if($item->id == 604)
                                                <option value="{{ $item->id }}"><strong>Medical(ORD1)</strong> - {{ $item->reviewer_name }}</option>
                                            @endif
                                            @if($item->id == 76)
                                                <option value="{{ $item->id }}"><strong>Medical(ORD2)</strong> - {{ $item->reviewer_name }}</option>
                                            @endif --}}
                                            @if($item->id == 4117)
                                                <option value="{{ $item->id }}"><strong>BD</strong> - {{ $item->reviewer_name }}</option>
                                            @endif
                                            @if($item->id == 5290)
                                                <option value="{{ $item->id }}"><strong>Biomedical Engineering</strong> - {{ $item->reviewer_name }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                       អនុម័តដំបូង | Initial Approval 2
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="verify_by_3">
                                        <option value="0"><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>






                            {{-- <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        ត្រួតពិនិត្យដោយសវនកម្មផ្ទៃក្នុង | Verified By​ internal Audit
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាមន្រ្តីហិរញ្ញវត្ថុ"
                                           data-placement="top"></i>
                                        <span style='color: red'>*</span></label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control select2" required name="reviewer">
                                        <option value=""><< ជ្រើសរើស | Select >></option>
                                        @foreach($reviewer as $key => $item)
                                            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select><br/>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <label>
                                        អនុម័តចុងក្រោយដោយ | Final Approved By 1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                           data-placement="top"></i>
                                        <span style='color: #ff0000'>*</span>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="final_short">
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
                                        អនុម័តចុងក្រោយដោយ | Final Approved By 2
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
                    {{-- <div class="row">
                        <label class="col-sm-3 col-form-label">
                            {{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                                <select required class="form-control js-reviewer-multi" name="reviewer_id[]" multiple="multiple">
                                    @foreach($reviewer as $item)
                                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}

                    {{-- <div class="row">
                        <label class="col-sm-3 col-form-label">
                            ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                             title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                             data-placement="top"></i>
                        </label>
                        <div class="col-sm-9 form-group">
                            <select class="form-control js-short-multi" name="review_short[]" multiple>
                                @foreach($reviewer as $item)
                                    <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                    {{-- <div class="row">
                        <label class="col-sm-3 col-form-label">
                            អនុម័តដោយ
                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                               title="សម្រាប់ MMI នឹងទៅដល់ President Approver ដោយស្វ័យប្រវត្ត"
                               data-placement="top">
                            </i>
                        </label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <select required class="form-control js-approver" name="approver_id" multiple="multiple">

                                    @foreach($approver as $item)
                                        <option
                                            value="{{ @$item->id }}"
                                        >
                                            {{ @$item->name }}({{ $item->position_name }})

                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}

                </fieldset>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('request_pr.store')  }}"
                        form="requestPR"
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

@include('request_pr.add_more_js')

@include('global.js_default_approve')
