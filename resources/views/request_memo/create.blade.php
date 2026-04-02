@extends('adminlte::page', ['activePage' => 'request_memo/create', 'titlePage' => __('User Management')])

@section('plugins.Select2', true)

@push('css')
    <style>
        .dropdown-fontname li {
            width: 200px;
            padding-left: 15px;
        }
    </style>
    @include('global.style_default_approve')
@endpush

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

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form
                  id="requestForm"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('request_memo.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

{{--            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestForm->id) }}">--}}
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('ទម្រង់សំណើរ') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                {{--<div class="row">--}}
                  {{--<div class="col-md-12 text-right">--}}
                      {{--<a href="{{ route('request.index') }}" class="btn btn-sm btn-primary">{{ __('ថយក្រោយ') }}</a>--}}
                  {{--</div>--}}
                {{--</div>--}}

                  <div class="row">
                      <div class="col-md-2">
                        <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control select2" data-reference="1234" id="company_id" name="company_id">
                          @foreach($company as $key => $value)
                            @if($value->id==Auth::user()->company_id)
                              <option value="{{ $value->id }}" data-reference="{{ $value->reference }}" selected="selected">{{ $value->name }}</option>
                            @else
                              <option value="{{ $value->id }}" data-reference="{{ $value->reference }}">{{ $value->name }}</option>
                            @endif
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
                              value="{{ old('no') }}"
                            >
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      <label class="col-sm-2 col-form-label">{{ __('សេចក្តី') }}<span style='color: red'>*</span></label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('no') ? ' has-danger' : '' }}">
                            <select class="form-control select2" name="type" id="type" required>
                              <option value="សេចក្តីសម្រេច">សេចក្តីសម្រេច</option>
                              <option value="សេចក្តីណែនាំ">សេចក្តីណែនាំ</option>
                              <option value="សេចក្តីជូនដំណឹង">សេចក្តីជូនដំណឹង</option>
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
                        <input type="text" class="form-control" id="for" name="for">
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
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                          @endforeach()
                        </select>
                      </div>
                    </div>
                  </div>

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
                                      value="{{ old('title_km') }}"
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
                                      value="{{ old('title_en') }}"
                              >
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      <label class="col-sm-2 col-form-label">យោងតាម(Reference)<span style='color: red'>*</span></label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('group_request') ? ' has-danger' : '' }}">
                            <?php $referenceCompany = $company->where('id', Auth::user()->company_id)->first(); ?>
                              <textarea
                                      id="reference"
                                      class="desc_textarea form-control{{ $errors->has('group_request') ? ' is-invalid' : '' }}"
                                      name="reference"
                                      required
                              ><div id="reference-value">{!! $referenceCompany->reference !!}</div></textarea>
                              @if ($errors->has('group_request'))
                                  <span class="error text-danger" for="reference">{{ $errors->first('group_request') }}</span>
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
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point1" class="error text-danger" for="point">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០២/Clause 02') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point2"
                              class="point_textarea form-control{{ $errors->has('point2') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point2" class="error text-danger" for="point2">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៣/Clause 03') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point3"
                              class="point_textarea form-control{{ $errors->has('point3') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point3" class="error text-danger" for="point3">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៤/Clause 04') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point4"
                              class="point_textarea form-control{{ $errors->has('point4') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point4" class="error text-danger" for="point4">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៥/Clause 05') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point5"
                              class="point_textarea form-control{{ $errors->has('point5') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point5" class="error text-danger" for="point5">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៦/Clause 06') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point6"
                              class="point_textarea form-control{{ $errors->has('point6') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point6" class="error text-danger" for="point6">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៧/Clause 07') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point7"
                              class="point_textarea form-control{{ $errors->has('point7') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point7" class="error text-danger" for="point7">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៨/Clause 08') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point8"
                              class="point_textarea form-control{{ $errors->has('point8') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point8" class="error text-danger" for="point8">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ០៩/Clause 09') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point9"
                              class="point_textarea form-control{{ $errors->has('point9') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point9" class="error text-danger" for="point9">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>

                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១០/Clause 10') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                              id="point10"
                              class="point_textarea form-control{{ $errors->has('point10') ? ' is-invalid' : '' }}"
                              name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point10" class="error text-danger" for="point10">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១១/Clause 11') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point11"
                                class="point_textarea form-control{{ $errors->has('point11') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point11" class="error text-danger" for="point11">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១២/Clause 12') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point12"
                                class="point_textarea form-control{{ $errors->has('point12') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point12" class="error text-danger" for="point12">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៣/Clause 13') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point13"
                                class="point_textarea form-control{{ $errors->has('point13') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point13" class="error text-danger" for="point13">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៤/Clause 14') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point14"
                                class="point_textarea form-control{{ $errors->has('point14') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point14" class="error text-danger" for="point14">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៥/Clause 15') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point15"
                                class="point_textarea form-control{{ $errors->has('point15') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point15" class="error text-danger" for="point15">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៦/Clause 16') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point16"
                                class="point_textarea form-control{{ $errors->has('point16') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point16" class="error text-danger" for="point16">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៧/Clause 17') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point17"
                                class="point_textarea form-control{{ $errors->has('point17') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point17" class="error text-danger" for="point17">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៨/Clause 18') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point18"
                                class="point_textarea form-control{{ $errors->has('point18') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point18" class="error text-danger" for="point18">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ១៩/Clause 19') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point19"
                                class="point_textarea form-control{{ $errors->has('point19') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point19" class="error text-danger" for="point19">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២០/Clause 20') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point20"
                                class="point_textarea form-control{{ $errors->has('point20') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point20" class="error text-danger" for="point20">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២១/Clause 21') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point21"
                                class="point_textarea form-control{{ $errors->has('point21') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point21" class="error text-danger" for="point21">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២២/Clause 22') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point22"
                                class="point_textarea form-control{{ $errors->has('point22') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point22" class="error text-danger" for="point22">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៣/Clause 23') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point23"
                                class="point_textarea form-control{{ $errors->has('point23') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point23" class="error text-danger" for="point23">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៤/Clause 24') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point24"
                                class="point_textarea form-control{{ $errors->has('point24') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point24" class="error text-danger" for="point24">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៥/Clause 25') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point25"
                                class="point_textarea form-control{{ $errors->has('point25') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point25" class="error text-danger" for="point25">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៦/Clause 26') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point26"
                                class="point_textarea form-control{{ $errors->has('point26') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point26" class="error text-danger" for="point26">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៧/Clause 27') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point27"
                                class="point_textarea form-control{{ $errors->has('point27') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point27" class="error text-danger" for="point27">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៨/Clause 28') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point28"
                                class="point_textarea form-control{{ $errors->has('point28') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point28" class="error text-danger" for="point28">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ២៩/Clause 29') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point29"
                                class="point_textarea form-control{{ $errors->has('point29') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
                              @if ($errors->has('point'))
                                  <span id="point29" class="error text-danger" for="point29">{{ $errors->first('point') }}</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  <div class="row point hidden">
                      <label class="col-sm-2 col-form-label">{{ __('ប្រការ ៣០/Clause 30') }}</label>
                      <div class="col-sm-10">
                          <div class="form-group{{ $errors->has('point') ? ' has-danger' : '' }}">
                            <textarea
                                id="point30"
                                class="point_textarea form-control{{ $errors->has('point30') ? ' is-invalid' : '' }}"
                                name="point[]"
                            >{{ old('point') }}</textarea>
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
                                      value="{{ old('file') }}"
                                  >
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-sm-12 ">
                      <div class="row">
                          <label class="col-sm-2 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                          <div class="col-sm-10">
                              <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                                  <textarea
                                          id="remark"
                                          class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                                          name="remark"
                                  ></textarea>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-sm-12" id="practise">
                      <div class="row">
                          <label class="col-sm-2 col-form-label">{{ __('ចម្លងជូន') }}<span style='color: red'>*</span></label>
                          <div class="col-sm-10">
                              <div class="form-group{{ $errors->has('practise_point') ? ' has-danger' : '' }}">
                                  <input
                                      type="number"
                                      min="1"
                                      max="30"
                                      id="practise_point"
                                      required
                                      class="form-control{{ $errors->has('practise_point') ? ' is-invalid' : '' }}"
                                      name="practise_point"
                                      value="{{ old('practise_point') }}"
                                  >
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="col-sm-12">
                      <div class="row">
                          <label class="col-sm-2 col-form-label">{{ __('ចាប់ផ្ដើមអនុវត្ត') }}<span style='color: red'>*</span></label>
                          <div class="col-sm-10">
                              <div class="form-group{{ $errors->has('title_en') ? ' has-danger' : '' }}">
                                  <input
                                      type="text"
                                      id="start_date"
                                      class="datepicker form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                                      name="start_date"
                                      required
                                      value="{{ old('start_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                                      data-inputmask-inputformat="dd-mm-yyyy"
                                      placeholder="dd-mm-yyyy"
                                      autocomplete="off"
                                  >
                              </div>
                          </div>
                      </div>

                      <!-- khmer date -->
                      <div class="row">
                          <div class="col-md-2">
                            <label>ចាប់ផ្ដើមអនុវត្ត(ខ្មែរ)<span style='color: red'>*</span></label>
                          </div>
                          <div class="col-md-10 form-group">
                              <input
                                    type="text"
                                    id="khmer_date"
                                    class="form-control{{ $errors->has('khmer_date') ? ' is-invalid' : '' }}"
                                    name="khmer_date"
                                    required="required"
                                    placeholder="ឧទាហរណ៍ៈ ថ្ងៃចន្ទ ១៤កើត ខែចេត្រ ឆ្នាំកុរ ឯកស័ក ពុទ្ធសករាជ ២៥៦៣"
                                    value="{{ old('khmer_date') }}"
                              >
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      <input type="hidden" name="" id="my_department" value="{{ Auth::user()->department_id }}">
                      <input type="hidden" name="" id="my_type" value="request">
                      <input type="hidden" name="" id="type_request" value="{{ config('app.type_memo') }}">
                      <input type="hidden" name="" id="type_report" value="">
                  </div>

                  <div class="col-sm-12">
                      <div class="row">
                          <div class="col-md-2">
                            <label>រៀបចំដោយ</label>
                          </div>
                          <div class="col-md-10">
                              <select class="form-control select2" name="created_by">
                                  @foreach($staffs as $key => $value)
                                      <option value="{{ $value->id }}" selected="selected">{{ $value->name }} ({{ $value->position->name_km }})</option>
                                  @endforeach()
                              </select><br/>
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
                      </legend>

                      <div class="col-sm-12 mt-3">
                          <div class="row">
                              <div class="col-md-2">
                                   <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ៖<span style='color: #ff0000'>*</span></label>
                              </div>
                              <div class="col-md-10 form-group">
                                  <select class="form-control select2 js-reviewer-multi" readonly="true" name="reviewers[]" required multiple="multiple">
                                      @foreach($reviewers as $item)
                                          <option value="{{ @$item->id }}">{{ @$item->name }}</option>
                                      @endforeach
                                  </select><br/>
                              </div>
                          </div>
                      </div>

                      <div class="col-sm-12">
                          <div class="row">
                              <div class="col-md-2">
                                   <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                              </div>
                              <div class="col-md-10 form-group">
                                  <select class="form-control js-approver" readonly="true" name="approver" required>
                                      <option value=""><< ជ្រើសរើស >></option>
                                      @foreach($approver as $item)
                                          <option value="{{ @$item->id }}" 
                                                  @if($item->id == @$defaul->approver) selected @endif>
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
                        formaction="{{ route('request_memo.store')  }}"
                        form="requestForm"
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

@include('request_memo.partials.js')

@include('global.js_default_approve')
