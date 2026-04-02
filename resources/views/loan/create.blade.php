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
        <div class="col-md-12">
          <form
              id="requestForm"
              method="POST"
              action="{{ route('loan.store') }}"
              enctype="multipart/form-data"
              class="form-horizontal">
            @csrf

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('សំណើរសុំឥណទាន') }}</h4>
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
                          value="{{ $value->id}}"
                          @if(Auth::user()->company_id == $value->id)) selected @endif
                        >
                          {{ $value->name }}
                        </option>
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-3">
                    <label>សាខា<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-sm-9 form-group">
                    <select class="form-control select2" required name="branch">
                      <option value=""><< ជ្រើសរើស >></option>
                      @foreach($branch as $key => $value)
                        <option
                          value="{{ $value->id }}"
                          @if(Auth::user()->branch_id == $value->id) selected @endif
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
                      <option value="1">ឥណទានថ្មី</option>
                      <option value="3">ឥណទានចាស់</option>
                      <!-- <option value="2">ឥណទានរៀបចំឡើងវិញ</option> -->
                      <option value="4">ឥណទានរៀបចំឡើងវិញលើកទី១</option>
                      <option value="5">ឥណទានរៀបចំឡើងវិញលើកទី២</option>
                      <option value="6">ឥណទានរៀបចំឡើងវិញលើកទី៣</option>
                      <option value="7">ឥណទានរៀបចំឡើងវិញលើកទី៤</option>
                      <option value="8">ឥណទានរៀបចំឡើងវិញលើកទី៥</option>
                      <option value="9">ឥណទានរៀបចំឡើងវិញលើកទី៦</option>
                      <option value="10">ឥណទានរៀបចំឡើងវិញលើកទី៧</option>
                      <option value="11">ឥណទានរៀបចំឡើងវិញលើកទី៨</option>
                      <option value="12">ឥណទានរៀបចំឡើងវិញលើកទី៩</option>
                      <option value="13">ឥណទានរៀបចំឡើងវិញលើកទី១០</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះមន្រ្តីឥណទាន<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" name="credit" id="credit" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះអ្នកខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" name="borrower" id="borrower" required>
                  </div>
                </div>

                <div class="row" id="profile">
                  <div class="col-md-3">
                    <label>ប្រវត្តរូបសង្ខេប (Check Black List)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <input type="text" placeholder="Name English" class="form-control" name="en_name" id="en_name" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <input 
                          type="text" 
                          placeholder="ថ្ងៃខែឆ្នាំកំណើត" 
                          class="datepicker form-control"
                          autocomplete="off"
                          name="dob" 
                          id="dob" required>
                      </div>
                      <div class="col-md-6 form-group">
                        <select class="form-control company select2" required id="id_types" name="id_types">
                          <option value=""><< ជ្រើសរើស >></option>
                            @foreach($id_types as $key => $value)
                              <option value="{{ $key }}">{{ $value->name_km }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <input type="text" placeholder="អត្តសញ្ញាណប័ណ្ឌ ឬលិខិតឆ្លងដែន" class="form-control" name="nid" id="nid">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឈ្មោះអ្នករួមខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select_tags" name="participants[]" id="participants" multiple="multiple">
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ទំហំឥណទាន(រៀល)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="text" class="form-control" name="money" id="money" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ប្រភេទរយៈពេលខ្ចី<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="type_time" id="type_time" required>
                      <option value="1">គិតជាខែ</option>
                      <option value="2">គិតជាសប្តាហ៍</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>រយៈពេលខ្ចី(ខែ/សប្តាហ៍)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input type="number" class="form-control" name="times" id="times" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>អត្រាការប្រាក់(%)<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <input class="form-control" type="number" min="1" step="0.1" name="interest" required>
                  </div>
                </div>

                <div id="ngo_service">
                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវារដ្ឋបាល(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control ngo_service_input" type="number" min="0" step="0.1" name="service">
                    </div>
                  </div>
                </div>

                <div id="mfi_service">
                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវារៀបចំឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" name="service_object[arrangement]">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវាត្រួតពិនិត្យឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" name="service_object[check]">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-3">
                      <label>សេវាប្រមូលឥណទាន(%)<span style='color: red'>*</span></label>
                    </div>
                    <div class="col-md-9 form-group">
                      <input class="form-control mfi_service_input" type="number" min="0" step="0.1" name="service_object[collection]">
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
                      <option value="1"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង </option>
                      <option value="2"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង </option>
                      <option value="3"> សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ </option>
                      <option value="4"> សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង </option>
                      <option value="5"> សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង </option>
                      <option value="6"> សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង </option>
                      <option value="7"> សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង </option>
                      <option value="8"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង </option>
                      <option value="9"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង </option>
                      <option value="10"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង </option>
                      <option value="11"> សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង </option>
                      <option value="12"> បង់រំលស់តែការប្រាក់រៀងរាល់ខែ </option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>របៀបអនុម័ត<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="principle" id="principle" required>
                      <option value="1"> អនុម័តតាមគោលការណ៍ </option>
                      <option value="2"> អនុម័តខុសគោលការណ៍ </option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>កំណត់សម្គាល់</label>
                  </div>
                  <div class="col-md-9 form-group">
                    <textarea class="form-control" rows="5" id="remark" name="remark"></textarea>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ឯកសារភ្ជាប់<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9">
                    <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                      <div id="validate"></div>
                      <input 
                          accept=".pdf" 
                          required
                          multiple
                          type="file"
                          id="file"
                          name="file[]"
                          value="{{ old('file') }}"
                      >
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
                        @if($item->id==Auth::id())
                          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
                        @endif
                      @endforeach()
                    </select><br/>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <label>ត្រួតពិនិត្យដោយ<span style='color: red'>*</span></label>
                  </div>
                  <div class="col-md-9 form-group">
                    <select class="form-control select2" name="reviewers[]" required multiple>
                      @foreach($reviewers as $item)
                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
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
                      @foreach($reviewers as $item)
                        <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
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
                    <select class="form-control select2" name="cc[]" multiple>
                      @foreach($reviewers as $item)
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
                    <select class="form-control" name="approve_by" id="approve_by" required>
                      <option value=""> << ជ្រើសរើស >> </option>
                      @foreach($approver as $item)
                        <option 
                          value="{{ $item->id }}" 
                          data-position_short="{{ $item->position_short_name }}"
                          > {{ $item->approver_name }}
                        </option>
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

@include('loan.partials.js')
