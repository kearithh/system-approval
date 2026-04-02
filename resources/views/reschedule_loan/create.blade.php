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
                        action="{{ route('reschedule_loan.store') }}"
                        class="form-horizontal">
                        @csrf
                        @method('post')

                        <div class="card ">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">{{ __('សំណើរសុំកែប្រែតារាងកាលវិភាគសងប្រាក់អតិថិជន') }}</h4>
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
                                    <div class="col-sm-2">
                                        <label>សាខា<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-sm-10 form-group">
                                        <select class="form-control select2" required name="branch_id">
                                            <option value=""><< ជ្រើសរើស >></option>
                                                @foreach($branch as $key => $value)
                                                    <option
                                                        value="{{ $value->id}}"
                                                        @if(Auth::user()->branch_id == $value->id) selected @endif
                                                    >
                                                        {{ $value->name_km }} ({{ $value->short_name }})
                                                    </option>
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
                                        >សំណើរសុំកែប្រែតារាងកាលវិភាគសងប្រាក់អតិថិជន តាមតារាងព័ត៌មានដូចខាងក្រោម៖</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
                                            <thead class="card-header ">
                                                <tr>
                                                    <td colspan="7" style="font-family: 'Khmer OS Muol Light'; text-align: center;">
                                                        ព័ត៌មានត្រឹមត្រូវ
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="min-width: 180px">ឈ្មោះអតិថិជន<span style='color: red'>*</span></th>
                                                    <th style="min-width: 160px">លេខគណនីកម្ចី<span style='color: red'>*</span></th>
                                                    <th style="min-width: 120px">ប្រាក់ដើម(៛)<span style='color: red'>*</span></th>
                                                    <th style="min-width: 110px">ការប្រាក់(%)<span style='color: red'>*</span></th>
                                                    <th style="min-width: 200px">រយះពេលខ្ចី(ខែ/សប្តាហ៍)<span style='color: red'>*</span></th>
                                                    <th style="min-width: 140px">សេវារដ្ឋបាល(%)<span style='color: red'>*</span></th>
                                                    <th style="min-width: 100px">អត្រាពិន័យ(%)<span style='color: red'>*</span></th>
                                                    <th style="min-width: 400px">របៀបសងរំលោះ<span style='color: red'>*</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="section">
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="text"
                                                            name="new[name]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="text"
                                                            name="new[account]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            id="balance"
                                                            type="text"
                                                            name="new[balance]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            name="new[interest]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <input
                                                                        class="col form-control"
                                                                        style="min-width: 100px;"
                                                                        min="1"
                                                                        type="number"
                                                                        name="new[term]"
                                                                        required
                                                                    >
                                                                </td>
                                                                <td>
                                                                    <select 
                                                                        class="col form-control"
                                                                        style="min-width: 100px;" 
                                                                        name="new[type_term]"
                                                                    >
                                                                        <option value="1">ខែ</option>
                                                                        <option value="2">សប្តាហ៍</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            name="new[services]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            name="new[penalty]"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <select class="form-control type" required name="new[type]">
                                                            <option value=""><< ជ្រើសរើស >></option>
                                                            @foreach($type as $key => $value)
                                                                <option value="{{ $value}}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="7" style="font-family: 'Khmer OS Muol Light'; text-align: center;">
                                                        ចំណុចខុស
                                                    </td>
                                                </tr>
                                                <tr class="section">
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="text"
                                                            name="old[name]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="text"
                                                            name="old[account]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            id="balance_old"
                                                            type="text"
                                                            name="old[balance]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="1"
                                                            step="0.01"
                                                            name="old[interest]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <input
                                                                        class="col form-control"
                                                                        style="min-width: 100px;"
                                                                        min="1"
                                                                        type="number"
                                                                        name="old[term]"
                                                                    >
                                                                </td>
                                                                <td>
                                                                    <select 
                                                                        class="col form-control"
                                                                        style="min-width: 100px;" 
                                                                        name="old[type_term]"
                                                                    >
                                                                        <option value="1">ខែ</option>
                                                                        <option value="2">សប្តាហ៍</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            name="old[services]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="col form-control"
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            name="old[penalty]"
                                                        >
                                                    </td>
                                                    <td>
                                                        <select class="type" name="old[type]">
                                                            <option value=""><< ជ្រើសរើស >></option>
                                                            @foreach($type as $key => $value)
                                                                <option value="{{ $value}}">{{ $value }}</option>
                                                            @endforeach()
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row" hidden>
                                    <div class="col-md-2">
                                        <label>មូលហេតុ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <textarea
                                            rows="7"
                                            class="form-control"
                                            name="reason"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>ឯកសារភ្ជាប់<span style='color: red'>*</span></label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                    <input
                                                        required
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
                                                    @endforeach()
                                                </select><br/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>មន្ត្រីគ្រប់គ្រង និងគាំទ្រប្រព័ន្ធ<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer select2" name="reviewers[0]" required>
                                            <option value=""><< ជ្រើសរើស >></option>
                                        @foreach($reviewers_mis as $item)
                                                <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ប្រធានតំបន់<span style='color: red'>*</span></label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer select2" name="reviewers[1]">
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($reviewers_rm as $item)
                                                <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <label>ប្រធាននាយកដ្ឋានប្រតិបត្តិការ</label>
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <select class="form-control reviewer select2" name="reviewers[2]">
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($reviewers_hoo as $item)
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
                                            <option value=""><< ជ្រើសរើស >></option>
                                            @foreach($approver as $item)
                                                <option value="{{ @$item->id }}" @if($item->id == 38) selected @endif>{{ @$item->approver_name }}</option>
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

@include('reschedule_loan.partials.js')
