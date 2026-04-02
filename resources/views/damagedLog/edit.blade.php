@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)

@section('btn_link')
  {{ route('request.index') }}
@stop
@section('btn_text')
  {{ __('Back') }}
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 text-right">
          <button id="back" class="btn btn-success btn-sm" style="margin-top: -35px"> Back</button>
        </div>
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('damagedlog.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf
            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Edit Damaged Log') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body ">

                  @include('damagedLog.partials.item_table_edit')

                  <br>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ឯកសារភ្ជាប់</label>
                    </div>
                    <div class="col-md-10">
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

                  <div class="row">
                    <div class="col-md-2">
                      <label>ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control company select2" name="company_id">
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

                  <div class="row">
                    <div class="col-md-2">
                      <label>បរិយាយមូលហេតុ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <textarea
                          class="point_textarea form-control"
                          name="desc"
                          required
                      >{{$data->desc}}</textarea>
                    </div>
                  </div>
                  @if($data->is_penalty == 1)
                    <div class="row">
                      <div class="col-md-2">
                        <label>សំណង<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control select2" name="is_penalty" id="is_penalty">
                          <option value="0">មិនផ្តល់សំណង</option>
                          <option value="1" selected>ផ្តល់សំណង</option>
                        </select>
                      </div>
                    </div>
                    <div class="penalty">
                      <div class="row">
                        <div class="col-md-2">
                          <label>ឈ្មោះអ្នកផ្តល់សំណង<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <input type="text" class="form-control" required value="{{@json_decode($data->penalty)->name}}" id="penalty_name" name="penalty[name]">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label>រូបិយប័ណ្ណ<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <select class="form-control select2" name="penalty[currency]" id="currency">
                            <option value="USD">ដុល្លារ</option>
                            <option value="KHR" @if(@json_decode(@$data->penalty)->currency == 'KHR') selected @endif>រៀល</option>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label>ទឹកប្រាក់<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <input type="number" step="0.01" id="amount" value="{{@json_decode(@$data->penalty)->amount}}" required class="form-control" name="penalty[amount]">
                        </div>
                      </div>
                    </div>
                    <div class="no_penalty" style="display: none">
                      <div class="row">
                        <div class="col-md-2">
                          <label>ហេតុផល<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <textarea class="form-control" rows="3" id="reason" name="penalty[reason]">{{json_decode($data->penalty)->reason}}</textarea>
                        </div>
                      </div>
                    </div>
                  @else
                    <div class="row">
                      <div class="col-md-2">
                        <label>សំណង<span style='color: red'>*</span></label>
                      </div>
                      <div class="col-md-10 form-group">
                        <select class="form-control select2" name="is_penalty" id="is_penalty">
                          <option value="0" selected>មិនផ្តល់សំណង</option>
                          <option value="1">ផ្តល់សំណង</option>
                        </select>
                      </div>
                    </div>
                    <div class="penalty" style="display: none">
                      <div class="row">
                        <div class="col-md-2">
                          <label>ឈ្មោះ<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <input type="text" class="form-control" value="{{@json_decode(@$data->penalty)->name}}" id="penalty_name" name="penalty[name]">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label>រូបិយប័ណ្ណ<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <select class="form-control select2" name="penalty[currency]" id="currency">
                            <option value="USD">ដុល្លារ</option>
                            <option value="KHR" @if(@json_decode(@$data->penalty)->currency == 'KHR') selected @endif>រៀល</option>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label>ទឹកប្រាក់<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <input type="number" step="0.01" id="amount" value="{{@json_decode(@$data->penalty)->amount}}" class="form-control" name="penalty[amount]">
                        </div>
                      </div>
                    </div>
                    <div class="no_penalty">
                      <div class="row">
                        <div class="col-md-2">
                          <label>ហេតុផល<span style='color: red'>*</span></label>
                        </div>
                        <div class="col-md-10 form-group">
                          <textarea class="form-control" required rows="3" id="reason" name="penalty[reason]">{{@json_decode(@$data->penalty)->reason}}</textarea>
                        </div>
                      </div>
                    </div>
                  @endif

                  <div class="row">
                    <div class="col-md-2">
                      <label>រៀបចំដោយ</label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="user_id" required>
                        @foreach($reviewer as $key => $value)
                          @if($value->id == $data->user_id)
                            <option value="{{ $value->id}}" selected="selected">{{ $value->reviewer_name }}</option>
                          @endif
                        @endforeach()
                      </select><br/>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-2">
                      <label>ពិនិត្យ និងបញ្ជូនបន្តដោយ<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-10 form-group">
                      <select class="form-control select2" name="review_by[]" required multiple>
                        
                        @foreach($data->reviewers() as $item)
                            <option value="{{ $item->id}}" selected="selected">
                              {{ $item->name }}({{$item->position_name}})
                            </option>
                        @endforeach

                        @foreach($reviewer as $key => $value)
                              <option value="{{ $value->id}}">{{  $value->reviewer_name }}</option>
                        @endforeach
                        
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <label class="col-sm-2 col-form-label">
                      ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                      <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                          title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                          data-placement="top"></i>
                    </label>
                    <div class="col-sm-10 form-group">
                      <select class="form-control select2" name="review_short[]" multiple>
                        @foreach($data->reviewers_short() as $item)
                          <option value="{{ $item->id }}" selected="selected">
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
                      <select class="form-control approver select2" name="approver" required>
                        @foreach($approver as $item)
                          <option value="{{ @$item->id }}" @if($item->id == @$data->approver()->id) selected @endif>{{ @$item->name }}({{$item->position_name}})</option>
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

@include('damagedLog.partials.add_more_js')

