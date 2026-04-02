@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request_grn.index') }}
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
                  id="requestGRN"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('request_grn.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestGRN->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('បណ្ណទទួលទំនិញ​/GRN') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  @include('request_grn.add_more_item_table')
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
                    <label class="col-sm-3 col-form-label">{{ __('PO CODE') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('code_po') ? ' has-danger' : '' }}">
                          <select class="form-control select2" id="code_po" name="code_po">
                          <option value=""> << ជ្រើសរើស >> </option>
                            @foreach($requestPO as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if(Auth::user()->code_po == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->code }}
                                </option>
                            @endforeach
                          </select>
                            @if ($errors->has('code_po'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('code_po') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('PR CODE') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('code_pr') ? ' has-danger' : '' }}">
                          <select class="form-control select2" id="code_pr" name="code_pr">
                          <option value=""> << ជ្រើសរើស >> </option>
                            @foreach($requestPR as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if(Auth::user()->code_pr == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->code }}
                                </option>
                            @endforeach
                          </select>
                            @if ($errors->has('code_pr'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('code_pr') }}</span>
                            @endif
                        </div>
                    </div>
                </div>


                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('ប្រគល់ដេាយ') }}</label>
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

                {{-- <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('ឈ្មោះអ្នកផ្តត់ផ្គង់') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('name_kh') ? ' has-danger' : '' }}">
                    <textarea
                            id="name_kh"
                            class="form-control{{ $errors->has('name_kh') ? ' is-invalid' : '' }}"
                            name="name_kh"
                    >@if($requestGRN){{ $requestGRN->name_kh }}@else{{ old('name_kh') }}@endif</textarea>
                            @if ($errors->has('name_kh'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_kh') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Vendor Name') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('name_en') ? ' has-danger' : '' }}">
                    <textarea
                            id="name_en"
                            class="form-control{{ $errors->has('name_en') ? ' is-invalid' : '' }}"
                            name="name_en"
                    >@if($requestGRN){{ $requestGRN->name_en }}@else{{ old('name_en') }}@endif</textarea>
                            @if ($errors->has('name_en'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_en') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Supplier Name: ') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('address_vd') ? ' has-danger' : '' }}">
                    <textarea
                            id="address_vd"
                            class="form-control{{ $errors->has('address_vd') ? ' is-invalid' : '' }}"
                            name="address_vd"
                    >@if($requestGRN){{ $requestGRN->address_vd }}@else{{ old('address_vd') }}@endif</textarea>
                            @if ($errors->has('address_vd'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_vd') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Delivery By: ') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('contact_ps') ? ' has-danger' : '' }}">
                    <textarea
                            id="contact_ps"
                            class="form-control{{ $errors->has('contact_ps') ? ' is-invalid' : '' }}"
                            name="contact_ps"
                    >@if($requestGRN){{ $requestGRN->contact_ps }}@else{{ old('contact_ps') }}@endif</textarea>
                            @if ($errors->has('contact_ps'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('contact_ps') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Tel') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('mobile_phone') ? ' has-danger' : '' }}">
                    <textarea
                            id="mobile_phone"
                            class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}"
                            name="mobile_phone"
                    >@if($requestGRN){{ $requestGRN->mobile_phone }}@else{{ old('mobile_phone') }}@endif</textarea>
                            @if ($errors->has('mobile_phone'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('mobile_phone') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div> --}}

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('Signature Vendor') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('filee') ? ' has-danger' : '' }}">
                            <input
                                type="file"
                                id="filee"
                                class="{{ $errors->has('filee') ? ' is-invalid' : '' }}"
                                name="filee"
                                value="{{ old('filee') }}"
                            >
                        </div>
                    </div>
                </div>

                {{-- <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('អាសយដ្ឋាន') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('address_kh') ? ' has-danger' : '' }}">
                    <textarea
                            id="address_kh"
                            class="form-control{{ $errors->has('address_kh') ? ' is-invalid' : '' }}"
                            name="address_kh"
                    >@if($requestGRN){{ $requestGRN->address_kh }}@else{{ old('address_kh') }}@endif</textarea>
                            @if ($errors->has('address_kh'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_kh') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Address(EN)') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('address_en') ? ' has-danger' : '' }}">
                    <textarea
                            id="address_en"
                            class="form-control{{ $errors->has('address_en') ? ' is-invalid' : '' }}"
                            name="address_en"
                    >@if($requestGRN){{ $requestGRN->address_en }}@else{{ old('address_en') }}@endif</textarea>
                            @if ($errors->has('address_en'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_en') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('VAT') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('vat_st') ? ' has-danger' : '' }}">
                    <textarea
                            id="vat_st"
                            class="form-control{{ $errors->has('vat_st') ? ' is-invalid' : '' }}"
                            name="vat_st"
                    >@if($requestGRN){{ $requestGRN->vat_st }}@else{{ old('vat_st') }}@endif</textarea>
                            @if ($errors->has('vat_st'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('vat_st') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>--}}

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('ឈ្មោះអ្នកទទួល (Receiver​ Name)') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('name_reciever') ? ' has-danger' : '' }}">
                    <textarea
                            id="name_reciever"
                            class="form-control{{ $errors->has('name_reciever') ? ' is-invalid' : '' }}"
                            name="name_reciever"
                    >@if($requestGRN){{ $requestGRN->name_reciever }}@else{{ old('name_reciever') }}@endif</textarea>
                            @if ($errors->has('name_reciever'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_reciever') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
              

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('DPT use: ') }}</label>
                    <div class="col-sm-">
                        <select class="form-control reviewer my_select" name="department">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($department as $key => $value)
                                                <option value="{{ $value->id }}">
                                                  {{ $value->name_en }}
                                                </option>
                                            @endforeach()
                                        </select>

                    </div>
                    </div>

                    {{-- <div class="col">
                    <label class="col-sm- col-form-label">{{ __('លេខទូរស័ព្ទ (Tel.​ No)') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('tel') ? ' has-danger' : '' }}">
                    <textarea
                            id="tel"
                            class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}"
                            name="tel"
                    >@if($requestGRN){{ $requestGRN->tel }}@else{{ old('tel') }}@endif</textarea>
                            @if ($errors->has('tel'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('tel') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-9">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              id="purpose"
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"
                              required
                      >@if($requestGRN){{ $requestGRN->purpose }}@else{{ old('purpose') }}@endif</textarea>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('មូលហេតុ') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                    <textarea
                            id="reason"
                            class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                            name="reason"
                    >@if($requestGRN){{ $requestGRN->reason }}@else{{ old('reason') }}@endif</textarea>
                            @if ($errors->has('reason'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                    <textarea
                            id="remark"
                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                            name="remark"
                    >@if($requestGRN){{ $requestGRN->remark }}@else{{ old('remark') }}@endif</textarea>
                            @if ($errors->has('remark'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                            @endif
                        </div>
                    </div>--}}
                  
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
                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_grn') }}">
                            <input type="hidden" name="" id="type_report" value="">
                        </div>
                    </legend>
                    
                    <div class="row">
                        <label class="col-sm-3 col-form-label">
                            ទទួលដេាយ<br>Received by<span style='color: red'>*</span>
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
                    </div>
                    
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

                    <div class="row">
                        <label class="col-sm-3 col-form-label">
                            បានពិនិត្យដេាយ<br>Checked by
                            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                               title="សម្រាប់ MMI នឹងទៅដល់ President Approver ដោយស្វ័យប្រវត្ត"
                               data-placement="top">
                            </i>
                        </label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <select required class="form-control js-approver" name="approver_id">
                                    <option value=""><<ជ្រើសរើស>></option>
                                    @foreach($approver as $item)
                                        <option 
                                            value="{{ @$item->id }}"
                                        >
                                            {{ @$item->name }}({{ $item->position_name }})
                                      </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </fieldset>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('request_grn.store')  }}"
                        form="requestGRN"
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

@include('request_grn.add_more_js')

@include('global.js_default_approve')
