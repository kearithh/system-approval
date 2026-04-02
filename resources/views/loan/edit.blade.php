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
              action="{{ route('loan.update', $data->id) }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('កែប្រែសំណើរសុំឥណទាន') }}</h4>
                <p class="card-category"></p>
              </div>
              <div class="card-body">

                <div class="row">
                  <div class="col-md-3">
                    <label>សម្រាប់ក្រុមហ៊ុន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control company select2" id="company" name="company_id">
                      @foreach($company as $key => $value)
                        <option
                          value="{{ $value->id }}"
                          @if($data->company_id == $value->id)) selected @endif
                        >
                          {{ $value->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>សាខា<span style='color: red'>*</span></label>
                  </div>

                  <div class="col-md-9 form-group">
                    <select class="form-control select2" required name="branch">
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                        <option
                          value="{{ $value->id }}"
                          @if($data->branch_id == $value->id) selected @endif
                        >
                          {{ $value->name_km }} ({{ $value->short_name }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ប្រភេទឥណទាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="type_loan" id="type_loan" required>
                      <option value="1" @if($data->type_loan == 1) selected @endif >ឥណទានថ្មី</option>
                      <option value="3" @if($data->type_loan == 3) selected @endif >ឥណទានចាស់</option>
                      <!-- <option value="2" @if($data->type_loan == 2) selected @endif >ឥណទានរៀបចំឡើងវិញ</option> -->
                      <option value="4" @if($data->type_loan == 4) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី១</option>
                      <option value="5" @if($data->type_loan == 5) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី២</option>
                      <option value="6" @if($data->type_loan == 6) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៣</option>
                      <option value="7" @if($data->type_loan == 7) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៤</option>
                      <option value="8" @if($data->type_loan == 8) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៥</option>
                      <option value="9" @if($data->type_loan == 9) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៦</option>
                      <option value="10" @if($data->type_loan == 10) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៧</option>
                      <option value="11" @if($data->type_loan == 11) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៨</option>
                      <option value="12" @if($data->type_loan == 12) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី៩</option>
                      <option value="13" @if($data->type_loan == 13) selected @endif >ឥណទានរៀបចំឡើងវិញលើកទី១០</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះមន្រ្តីឥណទាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" value="{{$data->credit}}" name="credit" id="credit" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះអ្នកខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" value="{{$data->borrower}}" name="borrower" id="borrower" required>
                  </div>
                </div>

                <div class="row" id="profile">
                  <div class="col-md-3">
                    <label>ប្រវត្តរូបសង្ខេប (Check Black List)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <input type="text" placeholder="Name English" class="form-control" value="{{@$data->aml->en_name}}" name="en_name" id="en_name" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <input 
                          type="text" 
                          placeholder="ថ្ងៃខែឆ្នាំកំណើត" 
                          class="datepicker form-control"
                          autocomplete="off"
                          name="dob" 
                          value="{{@$data->aml->dob}}" 
                          id="dob" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <select class="form-control company select2" required id="id_types" name="id_types">
                          <option value=""><< ជ្រើសរើស >></option>
                            @foreach($id_types as $key => $value)
                              <option value="{{ $key }}" @if(@$data->aml->id_types == $key) selected @endif>{{ $value->name_km }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <input type="text" value="{{@$data->aml->nid}}" placeholder="អត្តសញ្ញាណប័ណ្ឌ ឬលិខិតឆ្លងដែន" class="form-control" name="nid" id="nid">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះអ្នករួមខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <?php $part = json_decode($data->participants); ?>

                    <select class="form-control select_tags" name="participants[]" id="participants" multiple="multiple">
                     @if ($part)
                            @foreach($part as $value)
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @endforeach
                     @endif
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ទំហំឥណទាន(រៀល)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" value="{{$data->money}}" name="money" id="money" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ប្រភេទរយៈពេលខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="type_time" id="type_time" required>
                      <option value="1" @if($data->type_time == 1) selected @endif >គិតជាខែ</option>
                      <option value="2" @if($data->type_time == 2) selected @endif >គិតជាសប្តាហ៍</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>រយៈពេលខ្ចី(ខែ/សប្តាហ៍)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="number" class="form-control" value="{{$data->times}}" name="times" id="time" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>អត្រាការប្រាក់(%)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input class="form-control" type="number" value="{{$data->interest}}" min="1" step="0.1" name="interest" required>
                  </div>
                </div>

                <div id="ngo_service">
                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវារដ្ឋបាល(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control ngo_service_input" type="number" value="{{$data->service}}" min="0" step="0.1" name="service">
                    </div>
                  </div>
                </div>

                <div id="mfi_service">
                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវារៀបចំឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" 
                            name="service_object[arrangement]" value="{{ @$data->service_object->arrangement }}">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវាត្រួតពិនិត្យឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" 
                            name="service_object[check]" value="{{ @$data->service_object->check }}">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវាប្រមូលឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" 
                            name="service_object[collection]" value="{{ @$data->service_object->collection }}">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>របៀបសង<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select_tags" name="types" id="types" required>
                      <option value=""> << ជ្រើសរើស >> </option>
                      <option value="1" @if($data->types == 1) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង </option>
                      <option value="2" @if($data->types == 2) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង </option>
                      <option value="3" @if($data->types == 3) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ </option>
                      <option value="4" @if($data->types == 4) selected @endif > សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង </option>
                      <option value="5" @if($data->types == 5) selected @endif > សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង </option>
                      <option value="6" @if($data->types == 6) selected @endif > សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង </option>
                      <option value="7" @if($data->types == 7) selected @endif > សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង </option>
                      <option value="8" @if($data->types == 8) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង </option>
                      <option value="9" @if($data->types == 9) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង </option>
                      <option value="10" @if($data->types == 10) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង </option>
                      <option value="11" @if($data->types == 11) selected @endif > សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង </option>
                      <option value="12" @if($data->types == 12) selected @endif > បង់រំលស់តែការប្រាក់រៀងរាល់ខែ </option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>របៀបអនុម័ត<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="principle" id="principle" required>
                      <option value="1" @if($data->principle == 1) selected @endif > អនុម័តតាមគោលការណ៍ </option>
                      <option value="0" @if($data->principle == 0) selected @endif > អនុម័តខុសគោលការណ៍ </option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>កំណត់សម្គាល់</label>
                  </div>
                  <div class="col-md-9 form-group">
                    <textarea class="form-control" rows="5" id="remark" name="remark">{{$data->remark}}</textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឯកសារភ្ជាប់</label>
                  </div>
                  <div class="col-md-9">
                    <div class="row">

                      <div class="col-md-5 form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                        <div id="validate"></div>
                        <input
                            accept=".pdf" 
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
                  <div class="col-md-3">
                    <label>
                      ទីតាំង(GPS)
                      <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                        title="ទីតាំងអតិថិជន និងទ្រព្យបញ្ចាំ"
                        data-placement="top"></i>
                   </label>
                  </div>
                  <div class="col-md-9 form-group">
                    <table id="sections" class="table table-hover" >
                      @forelse($gps_object as $key => $item)
                        <tr class="section">
                          <td style="width: 10px;">
                            <button type="button"
                                    id="remove"
                                    class="remove btn btn-sm btn-danger"
                            >
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                          <td style="width: 250px">
                            <input type="text" class="form-control" name="gps_name[]" value="{{ $item->name }}" required placeholder="ឈ្មោះទីតាំង">
                          </td>
                          <td>
                            <input type="text" class="form-control" name="gps_link[]" value="{{ $item->link }}" required placeholder="តំណភ្ជាប់ទីតាំង Map">
                          </td>
                        </tr>
                      @empty
                        <tr class="section">
                          <td style="width: 10px;">
                            <button type="button"
                                    id="remove"
                                    class="remove btn btn-sm btn-danger"
                            >
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                          <td style="width: 250px">
                            <input type="text" class="form-control" name="gps_name[]" required placeholder="ឈ្មោះទីតាំង">
                          </td>
                          <td>
                            <input type="text" class="form-control" name="gps_link[]" required placeholder="តំណភ្ជាប់ទីតាំង Map">
                          </td>
                        </tr>
                      @endforelse
                      <tr id="add_more">
                        <td class="text-center">
                          <button type="button"
                                  id="addItem"
                                  class="addsection btn btn-sm btn-success"
                          >
                            <i class="fa fa-plus"></i>
                          </button>
                        </td>
                        <td colspan="2"></td>
                      </tr>
                    </table>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>រៀបចំដោយ</label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="user_id" required>
                      @foreach($staff as $item)
                        @if($item->id == $data->user_id)
                          <option value="{{ $item->id }}" selected="selected">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="reviewers[]" required multiple>

                      @foreach($data->reviewers() as $item)
                          <option value="{{ $item->id }}" selected="selected">
                            {{ $item->name }} ({{ $item->position_name }})
                          </option>
                      @endforeach

                      @foreach($reviewers as $key => $value)
                          <option value="{{ $value->id }}">{{ $value->reviewer_name }}</option>
                      @endforeach

                    </select>
                  </div>
                </div>

                <div class="row">
                  <label class="col-sm-3 col-form-label">
                    ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
                    <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                        title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
                        data-placement="top"></i>
                  </label>
                  <div class="col-sm-9 form-group">
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
                  <div class="col-md-3">
                    <label>ចម្លងជូន(CC)
                      <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                         title="ផ្នែកពាក់ព័ន្ធដែលជួយដឹងលឺ ជាទូទៅខាងផ្នែកប្រតិបត្តិការ..."
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

                <div class="row">
                  <div class="col-md-3">
                    <label>អនុម័តដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control" name="approve_by" id="approve_by" required >
                      @foreach($approver as $item)
                        <option 
                            value="{{ @$item->id }}"
                            data-position_short="{{ $item->position_short_name }}"
                            @if($item->id == @$data->approver()->id) selected @endif>{{ @$item->approver_name }}
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

@include('loan.partials.js')
