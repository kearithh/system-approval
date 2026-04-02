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
        @if (@auth()->user()->branch->branch == 1)
            <h2 style="color: red"> User in Branch can't use</h2>
            <?= die() ?>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form
                        enctype="multipart/form-data"
                        id="requestForm"
                        method="POST"
                        action="{{ route('request_ot.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('សំណើរសុំធ្វើការងារបន្ថែមម៉ោង(OT)') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control company my_select" id="company_id" name="company_id">
                                            @foreach($company as $key => $value)
                                                @if($value->id==Auth::user()->company_id)
                                                    <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }} ">{{ $value->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ឈ្មោះបុគ្គលិកថែមម៉ោង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer select2" name="staff" id="staff" required>
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($staffs as $item)
                                                <option 
                                                    value="{{ $item->id }}" 
                                                    data-system_user_id="{{ @$item->system_user_id }}"
                                                    data-position="{{ @$item->position_id }}"
                                                >{{ $item->reviewer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>មុខតំណែង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer my_select" id="position" name="position" required>
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($position as $key => $value)
                                                <option value="{{ $value->id }}">
                                                  {{ $value->name_km }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>អត្តលេខការងារ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <input type="number" name="staff_code" id="staff_code" 
                                            class="form-control"
                                            required
                                        >
                                        <input type="hidden" name="" id="is-staff-code">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <input type="button" 
                                            id="check-id"
                                            class="btn btn-info"
                                            required
                                            value="Check ID" 
                                        >
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <span id="message-id"></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ផ្នែក/នាយកដ្ឋាន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer my_select" name="department">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($department as $key => $value)
                                                <option value="{{ $value->id }}">
                                                  {{ $value->name_km }}
                                                </option>
                                            @endforeach()
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">ប្រភេទ OT<span class="text-danger">*</span></label>
                                    <div class="col-sm-10 form-group">
                                        <select class="form-control my_select" name="type" required>
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach(config('app.benefit_type') as $key => $value)
                                                <option value="{{ $value->val }}">
                                                    {{ $value->name_km }} | {{ $value->name_en }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ស្នើរសុំចាប់ពីថ្ងៃ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input
                                            type="text"
                                            id="start_date"
                                            class="datepicker form-control"
                                            name="start_date"
                                            required
                                            data-inputmask-inputformat="dd-mm-yyyy"
                                            placeholder="dd-mm-yyyy"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ដល់ថ្ងៃទី<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input
                                            type="text"
                                            id="end_date"
                                            class="datepicker form-control"
                                            name="end_date"
                                            required
                                            data-inputmask-inputformat="dd-mm-yyyy"
                                            placeholder="dd-mm-yyyy"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ចំនួនម៉ោង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <div id="validate"></div>
                                        <input 
                                            type="number"
                                            class="form-control validate_time"
                                            id="total" 
                                            name="total"
                                            step="1"
                                            min="0"
                                            value="0" 
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ចំនួននាទី<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input 
                                            type="number"
                                            class="form-control validate_time"
                                            id="total_minute"
                                            name="total_minute" 
                                            step="1"
                                            min="0"
                                            max="59"
                                            value="0" 
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ចាប់ពីម៉ោង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input
                                            type="time"
                                            id="start_time"
                                            class="form-control"
                                            name="start_time"
                                            required
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>រហូតដល់ម៉ោង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <input
                                            type="time"
                                            id="end_time"
                                            class="form-control"
                                            name="end_time"
                                            required
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>មូលហេតុ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control"
                                            name="reason"
                                            required
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ឯកសារភ្ជាប់</label>
                                            </div>
                                            <div class="col-md-10">
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
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>រៀបចំដោយ</label>
                                            </div>
                                            <div class="col-md-10 form-group">
                                                <select class="form-control my_select" name="user_id">
                                                    @foreach($staffs as $key => $value)
                                                        @if($value->id==Auth::id())
                                                            <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach()
                                                </select><br/>
                                            </div>
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
                                            <input type="hidden" name="" id="type_request" value="{{ config('app.type_request_ot') }}">
                                            <input type="hidden" name="" id="type_report" value="">
                                        </div>
                                    </legend>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <select class="form-control js-reviewer-multi" name="reviewers[]" multiple required>
                                                @foreach($reviewers as $item)
                                                    <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

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
                                            <select class="form-control js-short-multi" name="reviewers_short[]" multiple>
                                                @foreach($reviewers as $item)
                                                    <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                           <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                                        </div>
                                        <div class="col-md-10 form-group">
                                            <select class="form-control js-approver" name="approver" required>
                                                <option value=""> << ជ្រើសរើស >> </option>
                                                @foreach($approver as $item)
                                                    <option value="{{ @$item->id }}">
                                                        {{ @$item->name }}({{ @$item->position_name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </fieldset>

                            </div>
                            <div class="card-footer">
                                <button
                                    id="submit"
                                    type="submit"
                                    value="1"
                                    name="submit"
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

@include('request_OT.partials.js')

@include('global.js_default_approve')
