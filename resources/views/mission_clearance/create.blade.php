@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('cash_advance.create') }}
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
                <h4 class="card-title">{{ __('ទម្រង់ជម្រះបេសកកម្ម') }}</h4>
                <p class="card-category"></p>
              </div>
              <form 
                    enctype="multipart/form-data"
                    id="requestForm"
                    method="POST"
                    action="{{ route('mission_clearance.store') }}"
                    class="form-horizontal">
                @csrf
                @method('post')

              <div class="card-body">

                <div class="row">

                  <div class="col-md-12">
                    <small><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></small>
                        @include('mission_clearance.partials.item_table')
                   </div>
                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ឯកសារភ្ជាប់</label>
                            </div>
                            <div class="col-sm-9">
                            <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                <input 
                                    multiple="" 
                                    type="file"
                                    id="file"
                                    class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                    name="file[]"
                                    value="{{ old('file') }}"
                                >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                              <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
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
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>ការិយាល័យ<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="branch_id">
                                    @foreach($branch as $key => $value)
                                        <option value="{{ $value->id}}"
                                                @if(Auth::user()->branch_id == $value->id))
                                                selected
                                            @endif
                                        >
                                            {{ $value->name_km }} ({{ $value->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 form_clear">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ទឹកប្រាក់បុរេប្រទាន Advance(៛)<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <input class="form-control" value="0" type="number" id="advance" name="advance" autocomplete="off" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 form_clear">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ចំណាយសរុប(៛)<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <input class="form-control clear_advacne" type="number" readonly id="expense" name="expense" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">សរុបទឹកប្រាក់ចំណាយជាអក្សរ<span style='color: red'>*</span></label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="total_letter">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ទឹកប្រាក់ត្រូវបង់ចូលក្រុមហ៊ុន(៛)<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <input class="form-control" type="number" readonly id="company" name="company_transfer" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ទឹកប្រាក់ត្រូវបង់ឳ្យបុគ្គលិក(៛)<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <input class="form-control" type="number" readonly id="staff" name="staff_transfer" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                              <label>តំណភ្ជាប់ Advance</label>
                            </div>
                            <div class="col-md-9 form-group">
                              <select class="form-control select2" name="link">
                                <option value=""><< ជ្រើសរើស >></option>
                                @foreach($advance as $key => $value)
                                    <option value="{{ $value->id}}">
                                        {{ $value->title }}
                                        (Request By: {{ $value->user_name }}, Request Date: {{ $value->created_at }})
                                    </option>
                                @endforeach
                              </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">កំណត់សម្គាល់
                                <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                   title="ជាសំគាល់នៅបង្ហាញនៅខាងលើ Attacment"
                                   data-placement="top"></i>
                            </label>
                            <div class="col-sm-9">
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

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>រៀបចំស្នើសុំ<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-control reviewer select2" readonly="true" name="created_by" required>
                                    <option value="{{ @Auth::id() }}">{{ @Auth::user()->name }}</option>
                                </select><br/>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
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
                                    <input type="hidden" name="" id="type_request" value="{{ config('app.type_cash_advance') }}">
                                    <input type="hidden" name="" id="type_report" value="">
                                </div>
                            </legend>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: #ff0000'>*</span></label>
                                </div>  
                                <div class="col-md-9 form-group">
                                    <select required class="form-control js-reviewer-multi" name="reviewer_id[]" multiple="multiple">
                                        @foreach($reviewer as $item)
                                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  

                            <div class="row">
                                <div class="col-md-3">
                                    <label>ពិនិត្យដោយ(ហត្ថលេខាតូច)
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅខាងផ្នែកសវនាកម្ម..."
                                           data-placement="top"></i>
                                    </label>
                                </div>  
                                <div class="col-md-9 form-group">
                                    <select class="form-control js-short-multi" name="reviewer_short[]" multiple="multiple">
                                        @foreach($reviewer as $item)
                                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>ចម្លងជូន(CC)
                                        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                           title="ផ្នែកពាក់ព័ន្ធដែលជួយដឹងលឺ ជាទូទៅខាងផ្នែកហិរញ្ញវត្ថុ..."
                                           data-placement="top"></i>
                                    </label>
                                </div>  
                                <div class="col-md-9 form-group">
                                    <select class="form-control select2" name="cc[]" multiple="multiple">
                                        @foreach($reviewer as $item)
                                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                                </div>
                                <div class="col-md-9 form-group">
                                    <select class="form-control js-approver" required readonly="true" name="approver" required>
                                        @foreach($approver as $item)
                                            <option value="{{ @$item->id }}">
                                                {{ @$item->name }}({{ @$item->position_name }})
                                            </option>
                                        @endforeach
                                    </select><br/>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ទទួលដោយ<span style='color: #ff0000'>*</span></label>
                            </div>  
                            <div class="col-md-9">
                                <select required class="form-control receiver select2" name="receiver">
                                    @foreach($staffs as $item)
                                        <option value="{{ $item->id }}"
                                            @if(Auth::id() == $item->id))
                                                selected
                                            @endif
                                        >{{ $item->name }}</option>
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

@include('mission_clearance.partials.add_more_js')

@include('global.js_default_approve')