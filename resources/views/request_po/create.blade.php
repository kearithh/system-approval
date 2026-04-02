@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request_po.index') }}
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
                  id="requestPO"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('request_po.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestPO->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('បណ្ណបញ្ជាទិញ​/Purchase Order') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                  @include('request_po.add_more_item_table')
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
                    <label class="col-sm-3 col-form-label">{{ __('PR CODE') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('code_pr') ? ' has-danger' : '' }}">
                          <select required class="form-control select2" id="code_pr" name="code_pr[]" multiple>
                          <option value=""> << ជ្រើសរើស >> </option>
                            @foreach($requestPR as $key => $value)
                                <option value="{{ $value->code }}"
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
                    <label class="col-sm-3 col-form-label">{{ __('Exchange Rate') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('exchange_rate') ? ' has-danger' : '' }}">
                            <input type="text"
                                   id="exchange_rate"
                                   value="4100"
                                   class="form-control{{ $errors->has('exchange_rate') ? ' is-invalid' : '' }}"
                                   name="exchange_rate"
                                   readonly
                            >
                            @if ($errors->has('exchange_rate'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('exchange_rate') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                

                {{-- <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('Exchange Rate') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('exchange_rate') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px"
                            id="exchange_rate"
                            class="form-control{{ $errors->has('exchange_rate') ? ' is-invalid' : '' }}"
                            name="exchange_rate"
                            required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >@if($requestPO){{ $requestPO->exchange_rate }}@else{{ old('exchange_rate') }}@endif</textarea>
                            @if ($errors->has('exchange_rate'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('exchange_rate') }}</span>
                            @endif
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('អាករលើតម្លៃបន្ថែម/VAT') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('vat') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px"
                            id="vat"
                            class="form-control{{ $errors->has('vat') ? ' is-invalid' : '' }}"
                            name="vat"
                            required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >@if($requestPO){{ $requestPO->vat }}@else{{ old('vat') }}@endif</textarea>
                            @if ($errors->has('vat'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('vat') }}</span>
                            @endif
                        </div>
                    </div>
                </div>


                

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('ឈ្មោះអ្នកផ្តត់ផ្គង់') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('name_kh') ? ' has-danger' : '' }}">
                    <textarea
                            id="name_kh"
                            class="form-control{{ $errors->has('name_kh') ? ' is-invalid' : '' }}"
                            name="name_kh"
                    >@if($requestPO){{ $requestPO->name_kh }}@else{{ old('name_kh') }}@endif</textarea>
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
                    >@if($requestPO){{ $requestPO->name_en }}@else{{ old('name_en') }}@endif</textarea>
                            @if ($errors->has('name_en'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_en') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Address Vendor') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('address_vd') ? ' has-danger' : '' }}">
                    <textarea
                            id="address_vd"
                            class="form-control{{ $errors->has('address_vd') ? ' is-invalid' : '' }}"
                            name="address_vd"
                    >@if($requestPO){{ $requestPO->address_vd }}@else{{ old('address_vd') }}@endif</textarea>
                            @if ($errors->has('address_vd'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_vd') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Contact Person') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('contact_ps') ? ' has-danger' : '' }}">
                    <textarea
                            id="contact_ps"
                            class="form-control{{ $errors->has('contact_ps') ? ' is-invalid' : '' }}"
                            name="contact_ps"
                    >@if($requestPO){{ $requestPO->contact_ps }}@else{{ old('contact_ps') }}@endif</textarea>
                            @if ($errors->has('contact_ps'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('contact_ps') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('E-mail') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                    <textarea
                            id="email"
                            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                            name="email"
                    >@if($requestPO){{ $requestPO->email }}@else{{ old('email') }}@endif</textarea>
                            @if ($errors->has('email'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('Mobile Phone') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('mobile_phone') ? ' has-danger' : '' }}">
                    <textarea
                            id="mobile_phone"
                            class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}"
                            name="mobile_phone"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >@if($requestPO){{ $requestPO->mobile_phone }}@else{{ old('mobile_phone') }}@endif</textarea>
                            @if ($errors->has('mobile_phone'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('mobile_phone') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('VAT Vendor') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('vat_vd') ? ' has-danger' : '' }}">
                    <textarea
                            id="vat_vd"
                            class="form-control{{ $errors->has('vat_vd') ? ' is-invalid' : '' }}"
                            name="vat_vd"
                    >@if($requestPO){{ $requestPO->vat_vd }}@else{{ old('vat_vd') }}@endif</textarea>
                            @if ($errors->has('vat_vd'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('vat_vd') }}</span>
                            @endif
                        </div>
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
                    >@if($requestPO){{ $requestPO->address_kh }}@else{{ old('address_kh') }}@endif</textarea>
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
                    >@if($requestPO){{ $requestPO->address_en }}@else{{ old('address_en') }}@endif</textarea>
                            @if ($errors->has('address_en'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_en') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div> --}}

                <div class="row">
                <div class="col">
                    <label class="col-sm- col-form-label">{{ __('VAT') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('vat_st') ? ' has-danger' : '' }}">
                    <textarea
                            id="vat_st"
                            class="form-control{{ $errors->has('vat_st') ? ' is-invalid' : '' }}"
                            name="vat_st"
                    >@if($requestPO){{ $requestPO->vat_st }}@else{{ old('vat_st') }}@endif</textarea>
                            @if ($errors->has('vat_st'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('vat_st') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('ឈ្មោះអ្នកទទួល (Receiver​ Name)') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('name_reciever') ? ' has-danger' : '' }}">
                    <textarea
                            id="name_reciever"
                            class="form-control{{ $errors->has('name_reciever') ? ' is-invalid' : '' }}"
                            name="name_reciever"
                    >@if($requestPO){{ $requestPO->name_reciever }}@else{{ old('name_reciever') }}@endif</textarea>
                            @if ($errors->has('name_reciever'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_reciever') }}</span>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                <div class="col">
                    {{-- <label class="col-sm- col-form-label">{{ __('VAT') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('vat_st') ? ' has-danger' : '' }}">
                    <textarea
                            id="vat_st"
                            class="form-control{{ $errors->has('vat_st') ? ' is-invalid' : '' }}"
                            name="vat_st"
                    >@if($requestPO){{ $requestPO->vat_st }}@else{{ old('vat_st') }}@endif</textarea>
                            @if ($errors->has('vat_st'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('vat_st') }}</span>
                            @endif
                        </div>
                    </div> --}}
                    </div>

                    <div class="col">
                    <label class="col-sm- col-form-label">{{ __('លេខទូរស័ព្ទ (Tel.​ No)') }}</label>
                    <div class="col-sm-">
                        <div class="form-group{{ $errors->has('tel') ? ' has-danger' : '' }}">
                    <textarea
                            id="tel"
                            class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}"
                            name="tel"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    >@if($requestPO){{ $requestPO->tel }}@else{{ old('tel') }}@endif</textarea>
                            @if ($errors->has('tel'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('tel') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
                {{-- <div class="row">
                <label class="col-form-label">Share cost </label>
                <div class="col"> 
                <div class="form-group{{ $errors->has('ord_one') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="ORD1:"
                            id="ord_one"
                            class="form-control{{ $errors->has('ord_one') ? ' is-invalid' : '' }}"
                            name="ord_one"
                            
                    >@if($requestPO){{ $requestPO->ord_one }}@else{{ old('ord_one') }}@endif</textarea>
                            @if ($errors->has('ord_one'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('ord_one') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('ord_two') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="ORD2:"
                            id="ord_two"
                            class="form-control{{ $errors->has('ord_two') ? ' is-invalid' : '' }}"
                            name="ord_two"
                            
                    >@if($requestPO){{ $requestPO->ord_two }}@else{{ old('ord_two') }}@endif</textarea>
                            @if ($errors->has('ord_two'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('ord_two') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('orchid') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="Orchid: "
                            id="orchid"
                            class="form-control{{ $errors->has('orchid') ? ' is-invalid' : '' }}"
                            name="orchid"
                            
                    >@if($requestPO){{ $requestPO->orchid }}@else{{ old('orchid') }}@endif</textarea>
                            @if ($errors->has('orchid'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('orchid') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('spine') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="S-spine:  "
                            id="spine"
                            class="form-control{{ $errors->has('spine') ? ' is-invalid' : '' }}"
                            name="spine"
                            
                    >@if($requestPO){{ $requestPO->spine }}@else{{ old('spine') }}@endif</textarea>
                            @if ($errors->has('spine'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('spine') }}</span>
                            @endif
                        </div>
                    </div>
                </div> --}}

                <label class="col-form-label">TERM & CONDITIONS</label>
                <div class="row">
                <div class="col"> 
                <div class="form-group{{ $errors->has('incoterm') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="1.Incoterm:"
                            id="incoterm"
                            class="form-control{{ $errors->has('incoterm') ? ' is-invalid' : '' }}"
                            name="incoterm"
                    >@if($requestPO){{ $requestPO->incoterm }}@else{{ old('incoterm') }}@endif</textarea>
                            @if ($errors->has('incoterm'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('incoterm') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('payment') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="2.Payment:"
                            id="payment"
                            class="form-control{{ $errors->has('payment') ? ' is-invalid' : '' }}"
                            name="payment"
                    >@if($requestPO){{ $requestPO->payment }}@else{{ old('payment') }}@endif</textarea>
                            @if ($errors->has('payment'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('payment') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('delivery') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="3.Delivery: "
                            id="delivery"
                            class="form-control{{ $errors->has('delivery') ? ' is-invalid' : '' }}"
                            name="delivery"
                    >@if($requestPO){{ $requestPO->delivery }}@else{{ old('delivery') }}@endif</textarea>
                            @if ($errors->has('delivery'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('delivery') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('shipment') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="4.Shipment:  "
                            id="shipment"
                            class="form-control{{ $errors->has('shipment') ? ' is-invalid' : '' }}"
                            name="shipment"
                    >@if($requestPO){{ $requestPO->shipment }}@else{{ old('shipment') }}@endif</textarea>
                            @if ($errors->has('shipment'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('shipment') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                        <div class="col">
                <div class="form-group{{ $errors->has('warranty') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="5.Warranty:"
                            id="warranty"
                            class="form-control{{ $errors->has('warranty') ? ' is-invalid' : '' }}"
                            name="warranty"
                    >@if($requestPO){{ $requestPO->warranty }}@else{{ old('warranty') }}@endif</textarea>
                            @if ($errors->has('warranty'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('warranty') }}</span>
                            @endif
                        </div>
                    </div>
                <div class="col">
                <div class="form-group{{ $errors->has('consignee') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="6.Consignee: "
                            id="consignee"
                            class="form-control{{ $errors->has('consignee') ? ' is-invalid' : '' }}"
                            name="consignee"
                    >@if($requestPO){{ $requestPO->consignee }}@else{{ old('consignee') }}@endif</textarea>
                            @if ($errors->has('consignee'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('consignee') }}</span>
                            @endif
                        </div>
                    </div>
                {{-- <div class="col">
                <div class="form-group{{ $errors->has('notify_party') ? ' has-danger' : '' }}">
                    <textarea style="width: 100%; height: 40px" placeholder="7.Notify party:  "
                            id="notify_party"
                            class="form-control{{ $errors->has('notify_party') ? ' is-invalid' : '' }}"
                            name="notify_party"
                    >@if($requestPO){{ $requestPO->notify_party }}@else{{ old('notify_party') }}@endif</textarea>
                            @if ($errors->has('notify_party'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('notify_party') }}</span>
                            @endif
                        </div>
                    </div> --}}
                </div>

                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('គោលបំណង/Purpose') }}</label>
                  <div class="col-sm-9">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              id="purpose"
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"
                              
                      >@if($requestPO){{ $requestPO->purpose }}@else{{ old('purpose') }}@endif</textarea>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>

                {{-- <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('មូលហេតុ') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                    <textarea
                            id="reason"
                            class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                            name="reason"
                    >@if($requestPO){{ $requestPO->reason }}@else{{ old('reason') }}@endif</textarea>
                            @if ($errors->has('reason'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('reason') }}</span>
                            @endif
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                    <label class="col-sm-3 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                    <div class="col-sm-9">
                        <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                    <textarea
                            id="remark"
                            class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                            name="remark"
                    >@if($requestPO){{ $requestPO->remark }}@else{{ old('remark') }}@endif</textarea>
                            @if ($errors->has('remark'))
                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                  <label class="col-sm-3 col-form-label">{{ __('រៀបចំដោយ') }}</label>
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
                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_po_request') }}">
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
                                    <select class="form-control reviewer select2" required name="request_by">
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
                                        អនុម័តដំបូងដោយ | Initial approved by 1
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="agree_by_1">
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
                                        អនុម័តដំបូងដោយ | Initial approved by 2
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control reviewer select2" name="agree_by_2">
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
                                    <select class="form-control select2" required name="reviewer_by">
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
                                    <select class="form-control reviewer select2" name="reviewer_sh">
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


                </fieldset>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('request_po.store')  }}"
                        form="requestPO"
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

@include('request_po.add_more_js')

@include('global.js_default_approve')
