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
                  id=""
                  method="POST"
                  enctype="multipart/form-data"
                  action="{{ route('request_hr.update', $data->id) }}"
                  class="form-horizontal">
            @csrf
            @method('post')

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កែប្រែសំណើចំណាយទូទៅ | Modify general expense request') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                <div class="row">
                    <div class="col-md-12">
                        <small><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></small>
                        @include('request_hr.edit_item_table')
                    </div>
                    <div class="col-sm-12">
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label>ឯកសារភ្ជាប់</label>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                    <input
                                        type="file"
                                        id="file"
                                        class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                                        name="file"
                                        value="{{ old('file', $data->attachment) }}"
                                    >
                                    &emsp;&emsp;
                                    @if(@$data->attachment)
                                        <a href="{{ asset('/'.@$data->attachment) }}" target="_self">View old File</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
                            <div class="col-sm-9">
                                <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                                    <textarea
                                        id="remark"
                                        class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                                        name="remark"
                                    >@if($data){{ $data->remark }}@else{{ old('remark') }}@endif</textarea>
                                    @if ($errors->has('remark'))
                                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('remark') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <label class="col-sm-3 col-form-label">ទីតាំងការងារ | Location</label>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <input type="text" name="location" class="form-control" value="{{ @$data->location }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>សម្រាប់ក្រុមហ៊ុន</label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control select2" name="company_id">
                                    @foreach($company as $key => $value)
                                        <option value="{{ $value->id }}"
                                                @if($data->company_id == $value->id))
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
                            <div class="col-md-3">
                                <label>រៀបចំស្នើសុំ<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" readonly="true" name="created_by" required>
                                    <option value="{{ @$data->created_by }}">{{ @$data->requester->name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    យល់ព្រម | Initial approved (ហត្ថលេខាតូច | Small Signature)1
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាអ្នកគ្រប់គ្រងផ្ទាល់"
                                       data-placement="top"></i>
                                    <span style='color: red'>*</span> 
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2"  name="agree_by">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$agreeBy->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    យល់ព្រម | Initial approved (ហត្ថលេខាតូច | Small Signature)2
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជាប្រធានផ្នែក ឬប្រធាននាយកដ្ឋាន"
                                       data-placement="top"></i>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="agree_by_short">
                                    <option value="0"><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$agreeByShort->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                       <div class="row">
                            <div class="col-md-3">
                                <label>
                                    ត្រួតពិនិត្យ | Verification
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅជាមន្រ្តីហិរញ្ញវត្ថុ"
                                       data-placement="top"></i>
                                    <span style='color: red'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control select2"  @if(Auth::user()->company_id != 5)  @endif name="reviewer">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $key => $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$reviewer->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    អនុម័តដោយ | Final Approved 1
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                       data-placement="top"></i>
                                    <span style='color: #ff0000'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2"  name="reviewer_short_1">
                                    <option value=""><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ @$item->id }}" @if($item->id == @$reviewerShort1->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{--<div class="row">
                            <div class="col-md-3">
                                <label>
                                    អនុម័តដោយ | Final Approved 1
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ មានដូចជា ប្រធាននាយកប្រតិបត្តិសាម៉ី ជំនួយការប្រធាននាយកប្រតិបត្តិ"
                                       data-placement="top"></i>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" name="reviewer_short_2">
                                    <option value="0"><< ជ្រើសរើស >></option>
                                    @foreach($reviewers as $item)
                                        <option value="{{ $item->id }}" @if($item->id == @$reviewerShort2->user_id) selected @endif>
                                            {{ $item->reviewer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>--}}
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    អនុម័តដោយ | Final Approved 2
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="អនុម័តដោយ Chef Admin សំរាប់សំណើរចំណាយផ្សេងៗដែលមានតម្លៃច្រើនបំផុតត្រឹម 200,000 រៀល ឬ $50"
                                       data-placement="top"></i>
                                    <span style='color: #ff0000'>*</span>
                                </label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control reviewer select2" required name="approver" required>
                                    @foreach($approver as $item)
                                        <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                                            {{ @$item->name }}({{ @$item->position_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                @elseif ($data->status == config('app.approve_status_approve'))
                    <button
                        disabled
                        id="submit"
                        type="button"
                        value="1"
                        name="submit"
                        class="btn btn-danger">
                        Re-Submit
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

@include('request_hr.add_more_js')

