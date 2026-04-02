
<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('re.group-request-update') }}"
    class="form-horizontal">
    @csrf
    <input type="hidden" name="request_id" value="{{ @$data->id }}">

    <div class="row">
        <label class="col-sm-3 col-form-label">{{ __('ឈ្មោះរបាយការណ៍') }}<span style='color: #ff0000'>*</span></label>
        <div class="col-sm-9">
            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                <input
                    placeholder="ឈ្មោះរបាយការណ៍"
                    required
                    type="text"
                    id="name"
                    class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                    name="name"
                    value="{{ old('name', $data->name) }}"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-3 col-form-label">{{ __('សម្រាប់ថ្ងៃទី') }}<span style='color: red'>*</span></label>
        <div class="col-sm-9">
            <div class="form-group{{ $errors->has('end_date') ? ' has-danger' : '' }}">
                <input style="background-color: white" 
                    type="text"
                    id="end_date"
                    class="datepicker form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                    name="end_date"
                    required
                    readonly
                    value="{{ $endDate }}"
                    data-inputmask-inputformat="dd-mm-yyyy"
                    placeholder="dd-mm-yyyy"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-3 col-form-label">{{ __('ឯកសារយោង') }}</label>
        <div class="col-sm-5">
            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                <input
                    style="padding-top: 3px;"
                    type="file"
                    id="file"
                    accept="application/pdf"
                    class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                    name="file"
                >
            </div>
        </div>
        <div class="col-sm-4">

            @if(@$data->attachments)
                <a href="{{ asset('/'.@$data->attachments[0]['src']) }}" target="_blank">View old File</a><br>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 form-group">
            <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
        </div>
        <div class="col-md-9">
            <select class="form-control select2 company_id" id="company_id" name="company_id">
                @foreach($companies as $key => $value)
                    @if($value->id == $data->company_id)
                        <option selected value="{{ $value->id}}">{{ $value->name }}</option>
                    @endif
                @endforeach()
            </select><br/>
        </div>
    </div>

    @if (@$departments)
        <div class="row">
            <div class="col-md-3 form-group">
                <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
            </div>
            <div class="col-md-9 department_box" id="department_box">
                <select required class="form-control  select2 department_id" id="department_id" name="department_id">
                    @foreach($departments as $key => $value)
                        @if($value->id == $data->department_id)
                            <option selected value="{{ $value->id}} ">{{ @$value->name_km }}</option>
                        @endif
                    @endforeach()
                </select><br/>
            </div>
        </div>
    @endif

    @if (@$tags)
        <div class="row">
            <div class="col-md-3 form-group">
                <label>ប្រភេទ<span style='color: red'>*</span></label>
            </div>
            <div class="col-md-9">
                <select class="form-control select2" id="tags" name="tags">
                    @foreach($tags as $key => $value)
                        <option @if($value->slug == $data->tags) selected @endif value="{{ $value->slug }}">{{ @$value->name_km }}</option>
                    @endforeach()
                </select><br/>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <label>អ្នករៀបចំ<span style='color: #ff0000'>*</span></label>
        </div>
        <div class="col-md-9 form-group">
            <select class="form-control approver select2" name="user_id" required>
                <option value="NULL"> << ជ្រើសរើស >> </option>
                @foreach([Auth::user()] as $key => $value)
                    @if($value)
                        <option value="{{ $value->id}}" selected="selected">{{ $value->name }}</option>
                    @else
                        <option value="{{ $value->id}}">{{ $value->name }}</option>
                    @endif
                @endforeach()
            </select><br/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <label>អ្នកត្រួតពិនិត្យ 
                {{-- @if(Auth::id() != config('app.verify_report_id')) <span style='color: #ff0000'>*</span> @endif --}}
            </label>
        </div>
        <div class="col-md-9 form-group">
            <select
                {{-- @if(Auth::id() != config('app.verify_report_id')) required @endif --}}
                class="form-control select2"
                id="reviewers"
                name="reviewers[]"
                multiple
            >
                <!-- <option value=""> << ជ្រើសរើស >> </option> -->
                @foreach($data->getReviewerId() as $item)
                    <option value="{{ $item->reviewer_id}}" selected="selected">
                        {{ $item->reviewer_name }}
                    </option>
                @endforeach()

                @foreach(@$reviewers as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }} ({{ @$item->position_name }})</option>
                @endforeach()

            </select><br/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <label>ចម្លងជូន(CC)</label>
        </div>
        <div class="col-md-9 form-group">
            <select 
                class="form-control select2"
                id="cc"
                name="cc[]"
                multiple
            >
                <!-- <option value=""> << ជ្រើសរើស >> </option> -->
                @foreach(@$cc as $item)
                    <option @if (@in_array($item->id, @$data->cc)) selected @endif value="{{ @$item->id }}">
                        {{ @$item->name }} ({{ @$item->position_name }})
                    </option>
                @endforeach
            </select><br/>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <label>អ្នកអនុម័ត<span style='color: #ff0000'>*</span></label>
        </div>
        <div class="col-md-9 form-group approver_box">
            <select class="form-control select2" id="approver" name="approver" required>
                <!-- <option value=""> << ជ្រើសរើស >> </option> -->
                @foreach(@$approvers as $item)
                    <option @if($item->id == $data->getApproverId()) selected @endif value="{{ @$item->id }}">
                        {{ @$item->name }} ({{ @$item->position_name }})
                    </option>
                @endforeach
            </select><br/>
        </div>
    </div>
    
    <div class="text-right">
        <button
            type="submit"
            value="1"
            name="submit"
            class="btn btn-success">
            {{ __('កែប្រែ') }}
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">ចាកចេញ</button>
    </div>

</form>
