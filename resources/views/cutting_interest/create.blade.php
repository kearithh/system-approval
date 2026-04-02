@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
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
        <div class="col-md-12">
          <form
                  id="penalty"
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('penalty.store') }}"
                  class="form-horizontal">
            @csrf
            {{--@method('post')--}}

            <input type="hidden" class="request_token" name="request_type" value="{{ config('app.type_cutting_interest') }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើស្នើសុំបញ្ឈប់ការប្រាក់ និងកាត់ការប្រាក់ហួសកាលកំណត់') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('company_id') ? ' has-danger' : '' }}">
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

                <div class="row">
                    <label class="col-sm-2 col-form-label">សាខា<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('branch_id') ? ' has-danger' : '' }}">
                          <select class="form-control select2" name="branch_id">
                            @foreach($branch as $key => $value)
                                <option value="{{ $value->id }}"
                                        @if(Auth::user()->branch_id == $value->id))
                                            selected
                                        @endif
                                >
                                    {{ $value->name_km }} ({{ @$value->short_name }})
                                </option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                      <textarea
                              rows="4" 
                              id="purpose"
                              class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                              name="purpose"
                              required
                      >សំណើស្នើសុំបញ្ឈប់ការប្រាក់ និងកាត់ការប្រាក់ហួសកាលកំណត់ចំនួន ............ រៀល ចំពោះអតិថិជនយឺតយ៉ាវ (Loan Default) ដែលមានឈ្មោះ .......................... ភេទ........... CID៖ ........................ ថ្ងៃបើកប្រាក់ .......................... ចំនួនថ្ងៃយឺត ................. ថ្ងៃ។ </textarea>
                    </div>
                  </div>
                </div> -->

                @include('cutting_interest.partials.subject_interest')

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('មូលហេតុ') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                          <textarea
                                  id="reason"
                                  class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                                  name="reason"
                                  required
                          >ជំពាក់គេច្រើនគ្មានលទ្ធភាពសង ហើយអ្នកធានាជាអ្នកខចេញសងជុំនួស។</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('ពណ៌នាកម្មវត្ថុ') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('desc_purpose') ? ' has-danger' : '' }}">
                          <textarea 
                                  rows="4" 
                                  id="desc_purpose"
                                  class="form-control{{ $errors->has('desc_purpose') ? ' is-invalid' : '' }}"
                                  name="desc_purpose"
                                  required
                          >តបតាមកម្មវត្ថុ និងមូលហេតុ ដូចបានជំរាបជូនខាងលើ ខ្ញុំបាទមានកិត្តិយសជម្រាបជូន គណៈគ្រប់គ្រងមេត្តាជ្រាបថា ពាក់ព័ន្ធករណី អតិថិជនខាងលើសាខាបានចុះដោះស្រាយជាច្រើនលើកច្រើនសារទើបអតិថិជនគាត់យល់ព្រមសងតាមលក្ខខ័ណ្ឌដូចខាងក្រោម៖</textarea>
                        </div>
                    </div>
                </div>

                @include('cutting_interest.partials.calculate_interest')

                @include('cutting_interest.partials.add_more_item_table')

                <div class="row">
                    <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                            <div id="validate"></div>
                            <input
                                required
                                accept=".pdf" 
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
                  <label class="col-sm-2 col-form-label">{{ __('បរិយាយ') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('describe') ? ' has-danger' : '' }}">
                      <textarea
                              rows="7" 
                              id="describe"
                              class="desc_textarea form-control{{ $errors->has('describe') ? ' is-invalid' : '' }}"
                              name="describe"
                              required
                      >(១)៖ ម្តាយអតិថិជន បានព្រមព្រៀងបង់ជំនួស ..........................................................
                      <br>&emsp; &emsp; អាស្រ័យហេតុដូចបានជម្រាបជូនខាងលើសូមមេត្តាពិនិត្យ និងអនុម័ត លើការបញ្ឈប់ការប្រាក់ និង កាត់ការប្រាក់ហួសកាលកំណត់ ដោយព្រមទទួលយកការបង់ត្រឹមទឹកប្រាក់ដែលបានព្រមព្រៀងបង់ខាងលើដោយក្តីអនុគ្រោះ ។
                      <br>&emsp; &emsp; សូមគណៈគ្រប់គ្រង់ មេត្តាទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់អំពីខ្ញុំបាទ ។</textarea>
                    </div>
                  </div>
                </div>

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

                <div class="row">
                  <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                      <select required class="form-control select2 request-by-select2" name="user_id">
                        @foreach($requester as $item)
                          @if($item->id==Auth::id())
                              <option value="{{ $item->id }} " selected="selected">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
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
                  <label class="col-sm-2 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}<span style='color: red'>*</span></label>
                  <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                      <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">
                        @foreach($reviewer as $item)
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endforeach
                      </select>

                      @if ($errors->has('position_id'))
                        <span
                                id="name-error"
                                class="error text-danger"
                                for="input-name">
                          {{ $errors->first('position_id') }}
                        </span>
                      @endif
                    </div>
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
                    <select class="form-control select2" name="review_short[]" multiple>
                      @foreach($reviewer as $item)
                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                    <label class="col-sm-2 col-form-label">អនុម័តដោយ<span style='color: red'>*</span></label>
                    <div class="col-sm-10">
                        <div class="form-group">
                            <select required class="form-control select2 request-by-select2" name="approver_id">
                              <option value=""><<ជ្រើសរើស>></option>
                                @foreach($approver as $item)
                                    <option value="{{ $item->id }}">{{ $item->approver_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

              </div>
              <div class="card-footer">
                <button
                        type="submit"
                        value="1"
                        name="submit"
                        formaction="{{ route('penalty.store')  }}"
                        form="penalty"
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

@include('cutting_interest.partials.add_more_js')
