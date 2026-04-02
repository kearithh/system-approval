@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="card card-success collapsed-card">
    <div class="card-header">
        <i class="fa fa-greater-than"></i>
        បង្កើតឈ្មោះរបាយការណ៍<b class="text-danger"> (Template)</b>
        <div class="card-tools">
            <button type="button" class="btn btn-sm" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-sm" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body" style="display: none;">
        <form
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('re.item.store') }}"
            class="form-horizontal">
            @csrf

            <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('ឈ្មោះរបាយការណ៍') }}<span style='color: #ff0000'>*</span></label>
                <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                        <input
                            placeholder="ឈ្មោះរបាយការណ៍"
                            required
                            type="text"
                            id="name"
                            class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                            name="name"
                            value="{{ old('name') }}"
                        >
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 form-group">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                </div>
                <div class="col-md-10">
                    <select class="form-control select2 company_id" id="company_id" name="company_id">
                        @foreach($companies as $key => $value)
                            @if($value->id==Auth::user()->company_id)
                                <option value="{{ $value->id}}" data-reference="{{$value->reference}}" selected="selected">{{ $value->name }}</option>
                            @else
                                <option value="{{ $value->id}}" data-reference="{{$value->reference}}">{{ $value->name }}</option>
                            @endif
                        @endforeach()
                    </select><br/>
                </div>
            </div>

            @if (@$departments)
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label>នាយកដ្ឋាន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 department_box" id="department_box">
                        <select required class="form-control  select2 department_id" id="department_id" name="department_id">
                            <option value="{{ null }}"> << ជ្រើសរើស >> </option>
                            @foreach($departments as $key => $value)
                                @if($value->id == Auth::user()->department_id)
                                    <option value="{{ $value->id}} " selected="selected">{{ @$value->name_km }}</option>
                                @else
                                    <option value="{{ $value->id}} ">{{ @$value->name_km }}</option>
                                @endif
                            @endforeach()
                        </select><br/>
                    </div>
                </div>
            @endif

{{--            @if (@$branches)--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-2 form-group">--}}
{{--                        <label>សាខា</label>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-10">--}}
{{--                        <select class="form-control select2" id="branch_id" name="branch_id">--}}
{{--                            <option value=""> << ជ្រើសរើស >> </option>--}}
{{--                            @foreach($branches as $key => $value)--}}
{{--                                @if($value->id == Auth::user()->branch_id)--}}
{{--                                    <option value="{{ $value->id}} " selected="selected">--}}
{{--                                        {{ '('.@$value->code.'-'.@$value->short_name.') '.@$value->name_km }}--}}
{{--                                    </option>--}}
{{--                                @else--}}
{{--                                    <option value="{{ $value->id}} ">--}}
{{--                                        {{ '('.@$value->code.'-'.@$value->short_name.') '.@$value->name_km }}--}}
{{--                                    </option>--}}
{{--                                @endif--}}
{{--                            @endforeach()--}}
{{--                        </select><br/>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
            @if (@$tags)
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label>ប្រភេទ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control select2" id="tags" name="tags">
                            @foreach($tags as $key => $value)
                                <option value="{{ $value->slug}} ">
                                    {{ @$value->name_km }}
                                </option>
                            @endforeach()
                        </select><br/>
                    </div>
                </div>
            @endif

{{--            <div class="row">--}}
{{--                <label class="col-sm-2 col-form-label">{{ __('ថ្ងៃចាប់ផ្ដើម') }}<span style='color: red'>*</span></label>--}}
{{--                <div class="col-sm-10">--}}
{{--                    <div class="form-group{{ $errors->has('deadline') ? ' has-danger' : '' }}">--}}
{{--                        <input--}}
{{--                            type="text"--}}
{{--                            id="start_date"--}}
{{--                            class="datepicker form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}"--}}
{{--                            name="start_date"--}}
{{--                            required--}}
{{--                            value="{{ old('start_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"--}}
{{--                            data-inputmask-inputformat="dd-mm-yyyy"--}}
{{--                            placeholder="dd-mm-yyyy"--}}
{{--                        >--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <!-- <div class="row">
                <label class="col-sm-2 col-form-label">{{ __('ថ្ងៃផុតកំណត់​(Deadline)') }}<span style='color: red'>*</span></label>
                <div class="col-sm-10">
                    <div class="form-group{{ $errors->has('end_date') ? ' has-danger' : '' }}">
                        <input
                            type="text"
                            id="end_date"
                            class="datepicker form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                            name="end_date"
                            required
                            value="{{ old('end_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                            data-inputmask-inputformat="dd-mm-yyyy"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                        >
                    </div>
                </div>
            </div> -->

            <div class="row">
                <div class="col-md-2">
                    <label>អ្នករៀបចំ<span style='color: #ff0000'>*</span></label>
                </div>
                <div class="col-md-10 form-group">
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
                <div class="col-md-2">
                    <label>ចម្លងជូន(CC)</label>
                </div>
                <div class="col-md-10 form-group">
                    <select 
                        class="form-control select2"
                        id="cc"
                        name="cc[]"
                        multiple
                    >
                        <option value="{{ null }}"> << ជ្រើសរើស >> </option>
                        @foreach(@$reviewers as $item)
                            <option value="{{ @$item->id }}">{{ @$item->name }} ({{ @$item->position_name }})</option>
                        @endforeach
                    </select><br/>
                </div>
            </div>

{{--            <div class="row">--}}
{{--                <div class="col-md-2">--}}
{{--                    <label>អ្នកត្រួតពិនិត្យ<span style='color: #ff0000'>*</span></label>--}}
{{--                </div>--}}
{{--                <div class="col-md-10 form-group">--}}
{{--                    <select--}}
{{--                        class="form-control select2"--}}
{{--                        id="reviewers"--}}
{{--                        name="reviewers[]"--}}
{{--                        required--}}
{{--                        multiple--}}
{{--                    >--}}
{{--                        <option value="{{ null }}"> << ជ្រើសរើស >> </option>--}}
{{--                        @foreach(@$reviewers as $item)--}}
{{--                            <option value="{{ @$item->id }}">{{ @$item->name }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select><br/>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-md-2">--}}
{{--                    <label>អ្នកអនុម័ត<span style='color: #ff0000'>*</span></label>--}}
{{--                </div>--}}
{{--                <div class="col-md-10 form-group">--}}
{{--                    <select class="form-control select2" id="approver" name="approver" required>--}}
{{--                        --}}{{--                        <option value="{{ null }}"> << ជ្រើសរើស >> </option>--}}
{{--                        @foreach(@$approvers as $item)--}}
{{--                            <option value="{{ @$item->id }}">{{ @$item->name }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select><br/>--}}
{{--                </div>--}}
{{--            </div>--}}
            <button
                type="submit"
                value="1"
                name="submit"
                class="btn btn-success">
                {{ __('បញ្ជូន') }}
            </button>

        </form>
    </div>
</div>
