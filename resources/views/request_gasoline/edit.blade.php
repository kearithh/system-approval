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
                  action="{{ route('request_gasoline.update', $data->id) }}"
                  class="form-horizontal">
            @csrf
            @method('post')

            <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($data->id) }}">
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កែប្រែសំណើសុំថ្លៃសាំងរថយន្តចុះបេសកម្ម') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">
                <div class="row">
                  <div class="col-md-12">
                    <small><i>អ្នកស្នើសុំ ត្រូវបំពេញដោយការទទួលខុសត្រូវ | Requestor should fill in this request properly.</i></small>
                        @include('request_gasoline.partials.edit_item_table')
                    </div>
                    <div class="col-sm-12">
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
                                            <?php $atts = is_array($data->attachment) ? $data->attachment : json_decode($data->attachment); ?>
                                            @foreach($atts as $att )
                                                <a href="{{ asset($att->src) }}" target="_self">View old File: {{ $att->org_name }}</a><br>
                                            @endforeach
                                        @endif
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <select class="form-control select2" name="company_id">
                                    @foreach($company as $key => $value)
                                        <option value="{{ $value->id}}"
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

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ការិយាល័យ<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="branch_id">
                                    @foreach($branch as $key => $value)
                                        <option value="{{ $value->id}}"
                                                @if($data->branch_id == $value->id))
                                                selected
                                            @endif
                                        >
                                            {{ $value->name_km }} ({{ $value->short_name }})
                                        </option>
                                    @endforeach()
                                </select><br/>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ឈ្មោះបុគ្គលិក<span style='color: #ff0000'>*</span></label>
                            </div>  
                            <div class="col-md-9">
                                <select required class="form-control staff_id select2" name="staff_id">
                                    @foreach($staffs as $item)
                                        <option value="{{ $item->id }}"
                                            @if($item->id == @$data->staff_id) selected @endif
                                        >{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ម៉ាករថយន្ត<span style='color: #ff0000'>*</span></label>
                            </div>  
                            <div class="col-md-9">
                                <input class="form-control" type="text" id="model" name="model" value="{{ $data->model }}">
                            </div>
                        </div>  
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ថ្លៃសាំងក្នុងមួយលីត<span style='color: #ff0000'>*</span></label>
                            </div>  
                            <div class="col-md-9">
                                <input class="form-control" type="number" id="price_per_l" name="price_per_l" value="{{ $data->price_per_l }}">
                            </div>
                        </div>  
                    </div>

                    <div class="col-sm-12 form_clear">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ចំណាយសរុប(៛)<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9 form-group">
                                <input class="form-control clear_advacne" type="number" readonly id="total_expense" name="total_expense" autocomplete="off" value="{{ $data->total_expense }}">
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
                                    <option value="{{ @$data->created_by }}">{{ @$data->requester->name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label >{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}<span style='color: red'>*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select required class="form-control reviewer select2" name="reviewer_id[]" multiple="multiple">

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
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ពិនិត្យដោយ(ហត្ថលេខាតូច)
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                       title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ ជាទូទៅខាងផ្នែកសវនាកម្ម..."
                                       data-placement="top"></i>
                                </label>
                            </div>  
                            <div class="col-md-9">
                                <select class="form-control reviewer select2" name="reviewer_short[]" multiple="multiple">

                                    @foreach($data->reviewerShorts() as $item)
                                        <option value="{{ $item->id}}" selected="selected">
                                            {{ $item->name }}({{ $item->position_name }})
                                        </option>
                                    @endforeach

                                    @foreach($reviewers_short as $item)
                                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>  
                    </div>

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>ចម្លងជូន(CC)
                                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                                        title="ផ្នែកពាក់ព័ន្ធដែលជួយដឹងលឺ ជាទូទៅខាងផ្នែកប្រតិបត្តិការ..."
                                        data-placement="top"></i>
                                </label>
                            </div>  
                            <div class="col-md-9">
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

                    <div class="col-sm-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-control reviewer select2" required readonly="true" name="approver" required>
                                    @foreach($approver as $item)
                                        <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>
                                            {{ @$item->name }}({{ @$item->position_name }})
                                        </option>
                                    @endforeach
                                </select><br/>
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

@push('js')
    <script>

        $( "#back" ).on( "click", function( event ) {
            if(localStorage.previous){
                window.location.href = localStorage.previous;
                window.localStorage.removeItem('previous');
            }
            else{
                alert("Can't previous");
            }
        });

        $(document).on('click', '.append-datepicker', function(){
            $(this).datepicker({
                format: 'dd-mm-yyyy',
                todayHighlight:true
            }).focus();
        });
    </script>
@endpush

@include('request_gasoline.partials.add_more_js')

