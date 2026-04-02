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
                <div class="col-md-12">
                    <form
                        enctype="multipart/form-data"
                        id="requestForm"
                        method="POST"
                        action="{{ route('association.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('សមាគមសុវត្ថិភាពសហគមន៏') }}</h4>
                                <p class="card-category"></p>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="col-md-2">
                                      <label>សម្រាប់ក្រុមហ៊ុន</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                      <select class="form-control company select2" name="company_id">
                                        @foreach($company as $key => $value)
                                          @if($value->id==Auth::user()->company_id)
                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                                          @else
                                            <option value="{{ $value->id}} ">{{ $value->name }}</option>
                                          @endif
                                        @endforeach
                                      </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>កម្មវត្ថុ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control"
                                            name="purpose"
                                            required
                                        >សំណើសុំសំណងសេវាសង្រ្គោះគ្រួសារ ដល់អតិថិជនដែលបានស្លាប់ ដោយសារ...................................។</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>បរិយាយ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control point_textarea"
                                            name="description"
                                            required
                                        >
                                            តបតាមកម្មវត្ថុខាងលើ ខ្ញុំបាទស្នើសុំការអនុញ្ញតិពីលោកស្រីប្រធានសមាគមសុវត្ថិភាពសហគមន៍ ដើម្បីសុំដកប្រាក់ឱ្យអតិថិជនចំនួន 
                                            <b> ១,៥០០,០០០រៀល (មួយលានប្រាំរយពាន់រៀលគត់)</b> ដើម្បីជាសំណងដល់អតិថិជនដែលបានស្លាប់ដែលមានព័ត៌មានដូចខាងក្រោម៖
                                            <ul>
                                                <li>
                                                    អតិថិជនឈ្មោះ ..............  ភេទ.............អាយុ.........ឆ្នាំ ដែលបានស្លាប់កាលពីថ្ងៃទី...........ខែ..............ឆ្នាំ...........។
                                                </li>
                                                <li>
                                                    មានលេខគណនី.......................បើកប្រាក់ថ្ងៃទី..........ខែ..........ឆ្នាំ............ចំនួន.............................រៀល អាស័យដ្ឋាន ភូមិ............ ឃុំ......... ស្រុក.............ឃ្មុំ ខេត្ត..............។
                                                </li>
                                                <li>
                                                    អតិថិជនខាងលើនៅជំពាក់ប្រាក់ដើមចំនួន.....................រៀល ការប្រាក់......................រៀល សេរដ្ឋបាល............រៀល ប្រាក់ពិន័យចំនួន..................................រៀល ប្រាក់សរុបដើម្បីបង់ផ្តាច់ចំនួន.............................រៀល។
                                                </li>
                                            </ul>
                                            សំណងខាងលើអតិថិជនត្រូវបានទទួលប្រាក់ពី សហគ្រិនភាព ចំនួន.....................រៀល។
                                        </textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>បញ្ជាក់<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            class="form-control point_textarea"
                                            name="verify"
                                            required
                                        >
                                            ដើម្បីបញ្ជាក់បន្ថែមខ្ញុំបាទសូមភ្ជាប់មកជាមួយនូវឯកសារដូចមានរាយខាងក្រោម៖
                                            <ul>
                                                <li>
                                                    កំណត់ហេតុគ្រោះថ្នាក់របស់ប្រធានសាខា
                                                </li>
                                                <li>
                                                    កំណត់ហេតុគ្រោះថ្នាក់របស់អាជ្ញាធរពាក់ព័ន្ធ
                                                </li>
                                                <li>
                                                   សំបុត្រមរណភាពរបស់អតិថិជន
                                                </li>
                                                <li>
                                                    ឯកសារសម្គាល់អត្តសញ្ញាណ.......ប័ណ្ណសៀវភៅគ្រួសារ សំបុត្របញ្ជាក់កំណើត
                                                </li>
                                                <li>
                                                    រូបថតសកម្មភាពនៃកម្មវិធីបុណ្យសព
                                                </li>
                                                <li>
                                                    កិច្ចសន្យាចងការខ្ចីប្រាក់
                                                </li>
                                                <li>
                                                    ប័ណ្ណសេវាសង្រោ្គះគ្រួសារ
                                                </li>
                                                <li>
                                                    តារាងសងប្រាក់ (ជាភាសាខ្មែរ) និងប្រវត្តិសងប្រាក់
                                                </li>
                                            </ul>
                                        </textarea>
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
                                                <select class="form-control select2" name="user_id" required>
                                                    @foreach($staffs as $key => $value)
                                                        @if($value->id==Auth::id())
                                                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer select2" name="reviewers[]" required multiple>
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
                                        <select class="form-control approver select2" name="approver" required>
                                            @foreach($approver as $item)
                                                <option value="{{ @$item->id }}" @if($item->id == 11) selected @endif>{{ @$item->name }}({{$item->position_name}})</option>
                                            @endforeach
                                        </select>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('association.partials.js')
