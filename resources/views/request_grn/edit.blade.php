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
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
                </div>
                <div class="col-md-12">
                    <form
                        id="requestGRN"
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('request_grn.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែបណ្ណទទួលទំនិញ​/Edit GRN') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">
                                @include('request_grn.add_more_item_table_edit')
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                            <input
                                                type="file"
                                                id="file"
                                                class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                                name="file"
                                                value="{{ old('file', $data->attachment) }}"
                                            >
                                            &emsp;&emsp;
                                            @if(@$data->attachment)
                                                <a href="{{ asset('/'.@$data->attachment) }}" target="_self">View old File</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
                                            <select class="form-control company select2" name="company_id">
                                                @foreach($company as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                            @if($data->company_id == $value->id)
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
                                            <select class="form-control company select2" name="code_po">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                                @foreach($requestPO as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                            @if($data->code_po == $value->id)
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
                                            <select class="form-control company select2" name="code_pr">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                                @foreach($requestPR as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                            @if($data->code_pr == $value->id)
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
                                                    <option
                                                        @if($item->id == $data->user_id)
                                                            selected
                                                        @endif
                                                        value="{{ $item->id }}">{{ $item->name. ' ('.@$item->position->name_km.')' }}
                                                    </option>
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
                                            >@if($data){{ $data->name_kh }}@else{{ old('name_kh') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->name_en }}@else{{ old('name_en') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->address_vd }}@else{{ old('address_vd') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->contact_ps }}@else{{ old('contact_ps') }}@endif
                                            </textarea>
                                            @if ($errors->has('contact_ps'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('contact_ps') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                <label class="col-sm- col-form-label">{{ __('Tel:') }}</label>
                                    <div class="col-sm-">
                                        <div class="form-group{{ $errors->has('mobile_phone') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="mobile_phone"
                                                class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}"
                                                name="mobile_phone"
                                            >@if($data){{ $data->mobile_phone }}@else{{ old('mobile_phone') }}@endif
                                            </textarea>
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
                                                value="{{ old('filee', $data->attachment_vd) }}"
                                            >
                                            &emsp;&emsp;
                                            @if(@$data->attachment_vd)
                                                <a href="{{ asset('/'.@$data->attachment_vd) }}" target="_self">View old File</a>
                                            @endif
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
                                            >@if($data){{ $data->address_kh }}@else{{ old('address_kh') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->address_en }}@else{{ old('address_en') }}@endif
                                            </textarea>
                                            @if ($errors->has('address_en'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('address_en') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                </div> --}}

                                {{-- <div class="row">
                                <div class="col">
                                <label class="col-sm- col-form-label">{{ __('VAT') }}</label>
                                    <div class="col-sm-">
                                        <div class="form-group{{ $errors->has('vat_st') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="vat_st"
                                                class="form-control{{ $errors->has('vat_st') ? ' is-invalid' : '' }}"
                                                name="vat_st"
                                            >@if($data){{ $data->vat_st }}@else{{ old('vat_st') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->name_reciever }}@else{{ old('name_reciever') }}@endif
                                            </textarea>
                                            @if ($errors->has('name_reciever'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name_reciever') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            

                                <div class="row">
                                <div class="col">
                                <label class="col-sm- col-form-label">{{ __('សំរាប់ផ្នែក/For Department:') }}</label>
                                    <div class="col-sm-">
                                        <select class="form-control my_select" name="department">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($department as $key => $value)
                                                @if($value->id == $data->department_id)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name_en }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name_en }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                </div><br>
                                {{-- <div class="col">
                                <label class="col-sm- col-form-label">{{ __('លេខទូរស័ព្ទ (Tel.​ No)') }}</label>
                                    <div class="col-sm-">
                                        <div class="form-group{{ $errors->has('tel') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="tel"
                                                class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}"
                                                name="tel"
                                            >@if($data){{ $data->tel }}@else{{ old('tel') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->purpose }}@else{{ old('purpose') }}@endif
                                            </textarea>
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
                                            >@if($data){{ $data->reason }}@else{{ old('reason') }}@endif
                                            </textarea>
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
                                    >@if($data){{ $data->remark }}@else{{ old('remark') }}@endif</textarea>
                                            @if ($errors->has('remark'))
                                                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row">
                                    <label class="col-sm-3 col-form-label">ទទួលដេាយ<br>Received by<span style='color: red'>*</span></label>
                                    <div class="col-sm-9">
                                        <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                                            <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">

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
                                </div>

                                {{-- <div class="row">
                                    <label class="col-sm-3 col-form-label">
                                        ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                            title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                                            data-placement="top"></i>
                                    </label>
                                    <div class="col-sm-9 form-group">
                                        <select class="form-control select2" name="review_short[]" multiple>
                                            @foreach($data->reviewers_short() as $item)
                                                <option value="{{ $item->id }}" selected="selected">
                                                    {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers_short as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
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
                                            <select required class="form-control select2 request-by-select2" name="approver_id">
                                                @foreach($approver as $item)
                                                    <option value="{{ @$item->id }}" 
                                                            @if($item->id == @$data->approver()->id) 
                                                                selected 
                                                            @endif
                                                    >
                                                        {{ @$item->name }}({{ $item->position_name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                        id="submit"
                                        type="submit"
                                        value="1"
                                        name="resubmit"
                                        class="btn btn-info">
                                        {{ __('Re-Submit') }}
                                    </button>
                                @elseif ($data->status == config('app.approve_status_approve'))
                                    <button
                                        disabled
                                        id="submit"
                                        type="button"
                                        value="1"
                                        name="submit"
                                        class="btn btn-danger">
                                        Re-Submit
                                    </button>
                                @else
                                    <button
                                        @if ($data->user_id != \Illuminate\Support\Facades\Auth::id())
                                            disabled
                                            title="Only requester that able to edit the request"
                                        @endif
                                        id="submit"
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
@push('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>

        $( "#back" ).on( "click", function( event ) {
            if(localStorage.previous){
                window.location.href = localStorage.previous;
                window.localStorage.removeItem('previous');
            }
            else{
                alert("Can't previous");
            }
        });

        $(".company").change(function(){
            $value=$(this).val();
            //alert($value);
            $.ajax({
                type : 'get',
                url : "{{URL::route('request_grn.find_review')}}",
                data:{'company':$value},
                success:function(data){
                  $('.reviewer').empty();
                  $(".reviewer").html(data);
                }
            });
        });
    </script>
@endpush
@include('request_grn.add_more_js')
