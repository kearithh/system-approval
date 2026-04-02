@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])

@section('plugins.Select2', true)

@push('css')
    <style>
        .dropdown-fontname li {
            width: 200px;
            padding-left: 15px;
        }
    </style>

@endpush

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
                        method="POST"
                        enctype="multipart/form-data"
                        action="{{ route('request_memo.update', $memo->id) }}"
                        class="form-horizontal">
                        @csrf
                        @method('POST')

                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($memo->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('Edit Memo') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control select2" id="company_id" name="company_id">
                                            @foreach($company as $key => $value)
                                                <option
                                                    value="{{ $value->id}}"
                                                    data-reference="{{$value->reference}}"
                                                    @if($memo->company_id == $value->id))
                                                        selected
                                                    @endif
                                                >{{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('លេខរៀង') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                                            <input
                                                type="number"
                                                id="no"
                                                class="form-control{{ $errors->has('no') ? ' is-invalid' : '' }}"
                                                name="no"

                                                value="{{ old('no', $memo->no) }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                @if($memo->types == 'សេចក្តីណែនាំ')
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('សេចក្តី') }}<span style='color: red'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                                                <select id="type" class="form-control select2" name="type" required>
                                                    <option value="សេចក្តីសម្រេច">សេចក្តីសម្រេច</option>
                                                    <option value="សេចក្តីណែនាំ" selected>សេចក្តីណែនាំ</option>
                                                    <option value="សេចក្តីជូនដំណឹង">សេចក្តីជូនដំណឹង</option>
                                                    <option value="ការតម្លើងតួនាទី">ការតម្លើងតួនាទី</option>
                                                    <option value="ការតែងតាំង">ការតែងតាំង</option>
                                                    <option value="ការផ្លាស់ប្តូរតួនាទី">ការផ្លាស់ប្តូរតួនាទី</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="apply" style="display: block;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ជម្រាបជូន<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <input type="text" required value="{{$memo->apply_for}}" class="form-control" id="for" name="for">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="hr_request" style="display: none">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>តំណភ្ជាប់<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="hr" id="hr">
                                                    <option value=""><< ជ្រើសរើស >></option>
                                                    @foreach($hr as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                                @if($memo->hr_id == $value->id))
                                                                    selected
                                                                @endif
                                                        >{{ $value->name }}</option>
                                                    @endforeach()
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                @elseif($memo->types == 'សេចក្តីសម្រេច' || $memo->types == 'សេចក្តីជូនដំណឹង')
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('សេចក្តី') }}<span style='color: red'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                                                <select id="type" class="form-control select2" name="type" required>
                                                    <option value="សេចក្តីសម្រេច" 
                                                        @if($memo->types == 'សេចក្តីសម្រេច') selected @endif>សេចក្តីសម្រេច
                                                    </option>
                                                    <option value="សេចក្តីណែនាំ">សេចក្តីណែនាំ</option>
                                                    <option value="សេចក្តីជូនដំណឹង" 
                                                        @if($memo->types == 'សេចក្តីជូនដំណឹង') selected @endif>សេចក្តីជូនដំណឹង
                                                    </option>
                                                    <option value="ការតម្លើងតួនាទី">ការតម្លើងតួនាទី</option>
                                                    <option value="ការតែងតាំង">ការតែងតាំង</option>
                                                    <option value="ការផ្លាស់ប្តូរតួនាទី">ការផ្លាស់ប្តូរតួនាទី</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="apply" style="display: none">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ជម្រាបជូន<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <input type="text" value="{{$memo->apply_for}}" class="form-control" id="for" name="for">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hr_request" style="display: none">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>តំណភ្ជាប់<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="hr" id="hr">
                                                    <option value=""><< ជ្រើសរើស >></option>
                                                    @foreach($hr as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                                @if($memo->hr_id == $value->id))
                                                                    selected
                                                                @endif
                                                        >{{ $value->name }}</option>
                                                    @endforeach()
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('សេចក្តី') }}<span style='color: red'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                                                <select id="type" class="form-control select2" name="type" required>
                                                    <option value="សេចក្តីសម្រេច">សេចក្តីសម្រេច</option>
                                                    <option value="សេចក្តីណែនាំ">សេចក្តីណែនាំ</option>
                                                    <option value="សេចក្តីជូនដំណឹង">សេចក្តីជូនដំណឹង</option>
                                                    <option value="ការតម្លើងតួនាទី" 
                                                        @if($memo->types == 'ការតម្លើងតួនាទី') selected @endif>ការតម្លើងតួនាទី
                                                    </option>
                                                    <option value="ការតែងតាំង" 
                                                        @if($memo->types == 'ការតែងតាំង') selected @endif>ការតែងតាំង
                                                    </option>
                                                    <option value="ការផ្លាស់ប្តូរតួនាទី"
                                                        @if($memo->types == 'ការផ្លាស់ប្តូរតួនាទី') selected @endif>ការផ្លាស់ប្តូរតួនាទី
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="apply" style="display: none">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ជម្រាបជូន<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <input type="text" value="{{$memo->apply_for}}" class="form-control" id="for" name="for">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="hr_request">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>តំណភ្ជាប់<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control select2" name="hr" id="hr" required="required">
                                                    <option value=""><< ជ្រើសរើស >></option>
                                                    @foreach($hr as $key => $value)
                                                        <option value="{{ $value->id }}"
                                                                @if($memo->hr_id == $value->id))
                                                                    selected
                                                                @endif
                                                        >{{ $value->name }}</option>
                                                    @endforeach()
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                @endif

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ចំណងជើង(KM)') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('title_km') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="title_km"
                                                class="form-control{{ $errors->has('title_km') ? ' is-invalid' : '' }}"
                                                name="title_km"
                                                required
                                                value="{{ old('title_km',$memo->title_km) }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ __('ចំណងជើង(EN)') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('title_en') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="title_en"
                                                class="form-control{{ $errors->has('title_ne') ? ' is-invalid' : '' }}"
                                                name="title_en"
                                                value="{{ old('title_en', $memo->title_en) }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">យោងតាម(Reference)<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('group_request') ? ' has-danger' : '' }}">
                                            <textarea
                                                  id="reference"
                                                  class="desc_textarea form-control{{ $errors->has('group_request') ? ' is-invalid' : '' }}"
                                                  name="reference"
                                                  required
                                            ><div id="reference-value">{!! $memo->reference !!}</div></textarea>
                                        @if ($errors->has('group_request'))
                                            <span id="desc" class="error text-danger" for="reference">{{ $errors->first('group_request') }}</span>
                                        @endif
                                      </div>
                                    </div>
                                </div>

                                <div class="row point">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០១/Clause 01') }}<span style='color: red'>*</span></label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point1"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                                required
                                            >@if(isset($point[0])){{ $point[0] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point1" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[1])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០២/Clause 02') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point2"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[1])){{ $point[1] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point2" class="error text-danger" for="point2">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[2])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៣/Clause 03') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point3"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[2])){{ $point[2] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point3" class="error text-danger" for="point3">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[3])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៤/Clause 04') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point4"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[3])){{ $point[3] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point4" class="error text-danger" for="point4">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[4])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៥/Clause 05') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point5"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[4])){{ $point[4] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point5" class="error text-danger" for="point5">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[5])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៦/Clause 06') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point6"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[5])){{ $point[5] }}@else{{ old('point') }}@endif</textarea>
                                                                  @if ($errors->has('point'))
                                                <span id="point6" class="error text-danger" for="point6">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[6])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៧/Clause 07') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point7"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[6])){{ $point[6] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point7" class="error text-danger" for="point7">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[7])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៨/Clause 08') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point8"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[7])){{ $point[7] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point8" class="error text-danger" for="point8">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[8])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៩/Clause 09') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point9"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[8])){{ $point[8] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point9" class="error text-danger" for="point9">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[9])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១០/Clause 10') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point10"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[9])){{ $point[9] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point10" class="error text-danger" for="point10">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row point @if(!isset($point[10])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១១/Clause 11') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point11"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[10])){{ $point[10] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point11" class="error text-danger" for="point11">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[11])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១២/Clause 12') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point12"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[11])){{ $point[11] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point12" class="error text-danger" for="point12">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[12])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៣/Clause 13') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point13"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[12])){{ $point[12] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point13" class="error text-danger" for="point13">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[13])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៤/Clause 14') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point14"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[13])){{ $point[13] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point14" class="error text-danger" for="point14">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[14])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៥/Clause 15') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point15"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[14])){{ $point[14] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point15" class="error text-danger" for="point15">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[15])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៦/Clause 16') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point16"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[15])){{ $point[15] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point16" class="error text-danger" for="point16">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[16])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៧/Clause 17') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point17"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[16])){{ $point[16] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point17" class="error text-danger" for="point17">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[17])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៨/Clause 18') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point18"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[17])){{ $point[17] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point18" class="error text-danger" for="point18">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[18])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៩/Clause 19') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point19"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[18])){{ $point[18] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point19" class="error text-danger" for="point19">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[19])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២០/Clause 20') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point20"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[19])){{ $point[19] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point20" class="error text-danger" for="point20">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[20])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២១/Clause 21') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point21"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[20])){{ $point[20] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point21" class="error text-danger" for="point21">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[21])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២២/Clause 22') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point22"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[21])){{ $point[21] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point22" class="error text-danger" for="point22">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[22])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៣/Clause 23') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point23"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[22])){{ $point[22] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point23" class="error text-danger" for="point23">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[23])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៤/Clause 24') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point24"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[23])){{ $point[23] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point24" class="error text-danger" for="point24">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[24])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៥/Clause 25') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point25"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[24])){{ $point[24] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point25" class="error text-danger" for="point25">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[25])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៦/Clause 26') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point26"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[25])){{ $point[25] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point26" class="error text-danger" for="point26">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[26])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៧/Clause 27') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point27"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[26])){{ $point[26] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point27" class="error text-danger" for="point27">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[27])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៨/Clause 28') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point28"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[27])){{ $point[27] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point28" class="error text-danger" for="point28">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[28])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៩/Clause 29') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point29"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[28])){{ $point[28] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point29" class="error text-danger" for="point29">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row point @if(!isset($point[29])) hidden @endif">
                                    <label class="col-sm-2 col-form-label">{{ __('ប្រការ ៣០/Clause 30') }}</label>
                                    <div class="col-sm-10">
                                        <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                                            <textarea
                                                id="point30"
                                                class="point_textarea form-control{{ $errors->has('point') ? ' is-invalid' : '' }}"
                                                name="point[]"
                                            >@if(isset($point[29])){{ $point[29] }}@else{{ old('point') }}@endif</textarea>
                                            @if ($errors->has('point'))
                                                <span id="point30" class="error text-danger" for="point30">{{ $errors->first('point') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button"
                                                id="addPoint"
                                                class="btn btn-sm btn-success">
                                            <i class="fa fa-plus"></i>
                                            {{--                              {{ __('Add') }}--}}
                                        </button>
                                    </div>
                                </div>

                                <hr>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                <input
                                                    type="file"
                                                    id="file"
                                                    class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                                    name="file"
                                                    value="{{ old('file', $memo->attachment) }}"
                                                >
                                                &emsp;&emsp;
                                                @if(@$memo->attachment)
                                                    <a href="{{ asset('/'.@$memo->attachment) }}" target="_self">View old File</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                                        <div class="col-sm-10">
                                            <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                                                <textarea
                                                        id="remark"
                                                        class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                                                        name="remark"
                                                >@if($memo){{ $memo->remark }}@else{{ old('remark') }}@endif</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($memo->types == 'សេចក្តីណែនាំ' || $memo->types == 'សេចក្តីសម្រេច' || $memo->types == 'សេចក្តីជូនដំណឹង' )
                                    <div class="col-sm-12" id="practise" >
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label">{{ __('ចម្លងជូន') }}<span style='color: red'>*</span></label>
                                            <div class="col-sm-10 form-group">
                                                <input
                                                    type="number"
                                                    required
                                                    min="1"
                                                    max="20"
                                                    id="practise_point"
                                                    class="form-control{{ $errors->has('practise_point') ? ' is-invalid' : '' }}"
                                                    name="practise_point"
                                                    value="{{ old('practise_point', $memo->practise_point) }}"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-12" id="practise" style="display: none;">
                                        <div class="row">
                                            <label class="col-sm-2 col-form-label">{{ __('ចម្លងជូន') }}<span style='color: red'>*</span></label>
                                            <div class="col-sm-10 form-group">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    max="30"
                                                    id="practise_point"
                                                    class="form-control{{ $errors->has('practise_point') ? ' is-invalid' : '' }}"
                                                    name="practise_point"
                                                    value="{{ old('practise_point', $memo->practise_point) }}"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">រៀបចំដោយ</label>
                                        <div class="col-sm-10 form-group">
                                            <select class ="select2 form-control" name="created_by" disabled="true">
                                                @foreach($staffs as $key => $value)
                                                    <option
                                                        value="{{ $value->id }}"
                                                        @if($value->id == $memo->user_id) selected @endif >
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>                                

                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">
                                            {{ __('ចាប់ផ្ដើមអនុវត្ត') }}<span style='color: red'>*</span>
                                        </label>
                                        <div class="col-sm-10 form-group">
                                                <input
                                                    type="text"
                                                    id="start_date"
                                                    class="datepicker form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                                                    name="start_date"
                                                    required
                                                    value="{{ old('start_date', $memo->start_date->format('d/m/Y')) }}"
                                                    data-inputmask-alias="datetime"
                                                    data-inputmask-inputformat="dd/mm/yyyy"
                                                    autocomplete="off"
                                                    data-mask="" im-insert="true">
                                        </div>
                                    </div>
                                </div>

                                <!-- khmer date -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-md-2">
                                          <label>ចាប់ផ្ដើមអនុវត្ត(ខ្មែរ)<span style='color: red'>*</span></label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <input
                                                  type="text"
                                                  id="khmer_date"
                                                  class="form-control"
                                                  name="khmer_date"
                                                  placeholder="ឧទាហរណ៍ៈ ថ្ងៃចន្ទ ១៤កើត ខែចេត្រ ឆ្នាំកុរ ឯកស័ក ពុទ្ធសករាជ ២៥៦៣"
                                                  required
                                                  value="{{ old('khmer_date', $memo->khmer_date) }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">ពិនិត្យ និងបញ្ជូនបន្តដោយ៖<span style='color: red'>*</span></label>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                <select class ="select2 form-control" required="required" name="reviewers[]" multiple>

                                                    @foreach($memo->reviewers() as $item)
                                                        <option value="{{ $item->reviewer_id}}" selected="selected">
                                                            {{ $item->user_name }}({{$item->position_name}})
                                                        </option>
                                                    @endforeach

                                                    @foreach($reviewers as $key => $value)
                                                        <option value="{{ $value->id}}">{{ $value->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <select class="form-control approver select2" readonly="true" name="approver" required>
                                                @foreach($approver as $item)
                                                    <option 
                                                        value="{{ @$item->id }}" 
                                                        @if($item->id == @$memo->approver()->id) selected @endif
                                                    >
                                                        {{ @$item->name }}({{ $item->position_name }})
                                                    </option>
                                                @endforeach
                                            </select><br/>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">

                                @if ($memo->status == config('app.approve_status_reject'))
                                    <button
                                        @if ($memo->user_id != \Illuminate\Support\Facades\Auth::id())
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
                                @else
                                    <button
                                        @if ($memo->user_id != \Illuminate\Support\Facades\Auth::id())
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

@include('request_memo.partials.js')

