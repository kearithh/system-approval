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
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
                </div>
                <div class="col-md-12">
                    <form
                        enctype="multipart/form-data"
                        id="requestForm"
                        method="POST"
                        action="{{ route('request_ot.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែសំណើរសុំធ្វើការងារបន្ថែមម៉ោង(OT)') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control company my_select" name="company_id">
                                            @foreach($company as $key => $value)
                                                @if($value->id == $data->company_id)
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
                                        <select class="form-control select2" name="staff" id="staff" required>
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            <option value="{{ $data->staff }}" selected>{{ $data->staff }}</option>
                                            @foreach($staffs as $item)
                                                @if($item->id == $data->staff)
                                                    <option 
                                                        value="{{ $item->id }}" 
                                                        data-system_user_id="{{ @$item->system_user_id }}"
                                                        data-position="{{ @$item->position_id }}"
                                                        selected>{{ $item->reviewer_name }}
                                                    </option>
                                                @else
                                                    <option 
                                                        value="{{ $item->id }}"
                                                        data-system_user_id="{{ @$item->system_user_id }}"
                                                        data-position="{{ @$item->position_id }}"
                                                        >{{ $item->reviewer_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>មុខតំណែង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control my_select" id="position" name="position" required>
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($position as $key => $value)
                                                @if($value->id == $data->position_id)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name_km }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name_km }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>អត្តលេខការងារ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <input type="number" 
                                            name="staff_code" 
                                            id="staff_code" 
                                            value="{{ $data->staff_code }}"
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
                                        <select class="form-control my_select" name="department">
                                            <option value=""> << ជ្រើសរើស >> </option>
                                            @foreach($department as $key => $value)
                                                @if($value->id == $data->department_id)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name_km }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name_km }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-2 col-form-label">ប្រភេទ OT<span class="text-danger">*</span></label>
                                    <div class="col-sm-10 form-group">
                                        <select class="form-control select2" name="type">
                                            @foreach(config('app.benefit_type') as $key => $value)
                                                <option value="{{ $value->val }}" @if($value->val == $data->type) selected @endif>
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
                                            placeholder="dd-mm-yyyy"
                                            autocomplete="off"
                                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($data->start_date))->format('d-m-Y'))}}"
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
                                            placeholder="dd-mm-yyyy"
                                            autocomplete="off"
                                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($data->end_date))->format('d-m-Y'))}}"
                                        >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ចំនួនម៉ោង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <div id="validate"></div>
                                        <input type="number"
                                            class="form-control validate_time"
                                            id="total"
                                            name="total"
                                            step="1"
                                            min="0"
                                            value="{{$data->total}}" 
                                            required
                                        ></textarea>
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
                                            value="{{$data->total_minute}}" 
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
                                            value="{{$data->start_time}}"
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
                                            value="{{$data->end_time}}"
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
                                        >{{$data->reason}}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                    <label>ឯកសារភ្ជាប់</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                <input
                                                    type="file"
                                                    id="file"
                                                    name="file[]"
                                                    multiple="multiple"
                                                    value="{{ old('file') }}"
                                                >
                                            </div>

                                            <div class="col-md-7">
                                                @if(@$data->attachment)
                                                    <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                                    @foreach($atts as $att )
                                                        <a href="{{ asset($att->src) }}" target="_self">View old File: {{ $att->org_name }}</a><br>
                                                    @endforeach
                                                @endif
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
                                                <select class="form-control my_select" name="user_id" required>
                                                    @foreach($staffs as $key => $value)
                                                        @if($value->id == $data->user_id)
                                                            <option value="{{ $value->id }} " selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach()
                                                </select><br/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer my_select" name="reviewers[]" required multiple>

                                            @foreach($data->reviewers() as $item)
                                                <option value="{{ $item->id }}" selected="selected">
                                                  {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
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
                                        <select class="form-control my_select" name="reviewers_short[]" multiple>

                                            @foreach($data->reviewers_short() as $item)
                                                <option value="{{ $item->id}}" selected="selected">
                                                    {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers_short as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                       <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control approver my_select" name="approver" required>
                                            @foreach($approver as $item)
                                                <option value="{{ @$item->id }}" 
                                                    @if($item->id == @$data->approver()->id) selected @endif>
                                                    {{ @$item->name }}({{ @$item->position_name }})
                                                </option>
                                            @endforeach
                                        </select><br/>
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

@include('request_OT.partials.js')
