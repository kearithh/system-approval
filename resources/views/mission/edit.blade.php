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
                        action="{{ route('mission.update', $data->id) }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('កែប្រែលិខិតបញ្ជាបេសកកម្ម') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control company select2" name="company_id">
                                            @foreach($company as $key => $value)
                                                @if($value->id == $data->company_id)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <?php
                                    $branches = is_array($data->branch) ? $data->branch : json_decode($data->branch);
                                    $branchId = collect($branches)->pluck('branch_id')->toArray();
                                ?>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>សាខា / ទីតាំង ចុះបេសកកម្ម<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control select_tag" required name="branch[]" multiple>
                                            @foreach($branches as $key => $value)
                                                <option value="{{ $value->branch_id }} " selected >{{ $value->branch_name }}</option>
                                            @endforeach
                                            @foreach($branch as $key => $value)
                                                @if(!in_array($value->id, $branchId))
                                                    <option value="{{ $value->id }}">{{ $value->name_km }}</option>
                                                @endif
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>បុគ្គលិកបំពេញបេសកកម្ម<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control reviewer select2" name="staffs[]" required multiple>
                                            @foreach($staff_use as $key => $value)
                                                <option value="{{ $value->staff_id }}" selected>{{ $value->staff_name }}({{ $value->position }})</option>
                                            @endforeach

                                            @foreach($staffs as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->staff_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>គោលបំណង<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea
                                            class="form-control"
                                            name="purpose"
                                            required
                                        >{{$data->purpose}}</textarea><br>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>ថ្ងៃចេញដំណើរ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
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
                                    <div class="col-md-3">
                                        <label>ថ្ងៃត្រឡប់មកវិញ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
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
                                    <div class="col-md-3">
                                        <label>មធ្យោបាយធ្វើដំណើរ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <textarea
                                            class="form-control"
                                            name="transportation"
                                            required
                                        >{{$data->transportation}}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>សេចក្តីស្នើរសុំ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <textarea
                                            class="form-control"
                                            name="respectfully"
                                            required
                                        >{{ $data->respectfully }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                    <label>ឯកសារភ្ជាប់</label>
                                    </div>
                                    <div class="col-md-9">
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
                                                    <?php 
                                                        $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); 
                                                    ?>
                                                    @foreach($atts as $att )
                                                        <a href="{{ asset($att->src) }}" target="_self">
                                                            View old File: {{ $att->org_name }}
                                                        </a><br>
                                                    @endforeach
                                                @endif
                                          </div>

                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>រៀបចំដោយ</label>
                                            </div>
                                            <div class="col-md-9 form-group">
                                                <select class="form-control select2" name="user_id" required>
                                                    <option value="{{ $data->user_id }}" selected>
                                                        {{ @auth::user()->name }}
                                                    </option>
                                                </select><br/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ</label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control reviewer select2" name="reviewers[]" multiple>

                                            @foreach($data->reviewers() as $item)
                                                <option value="{{ $item->id}}" selected>
                                                    {{ $item->name }}({{ $item->position_name }})
                                                </option>
                                            @endforeach

                                            @foreach($reviewers as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 mb-3">
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
                                                @foreach($data->cc() as $item)
                                                    <option value="{{ $item->id}}" selected="selected">
                                                        {{ $item->name }}({{ $item->position_name }})
                                                    </option>
                                                @endforeach

                                                @foreach($cc as $item)
                                                    <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                       <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <select class="form-control approver select2" name="approver" required>
                                            @foreach($approver as $item)
                                                <option value="{{ @$item->id }}" 
                                                        @if($item->id == @$data->approver()->id) selected @endif>
                                                    {{ @$item->name }}({{ @$item->position_name }})
                                                </option>
                                            @endforeach
                                        </select>
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

@include('mission.partials.js')
