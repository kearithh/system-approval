<div class="card-body ">
    @include('general_request.partials.item_packing_edit')

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
        <div class="col-sm-10">
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

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('សម្រាប់ក្រុមហ៊ុន') }}</label>
        <div class="col-sm-10">
            <div class="form-group">
                <select class="form-control company select2" name="company_id">
                    @foreach($company as $key => $value)
                        <option value="{{ $value->id }}"
                                @if ($data->company_id == $value->id)) selected @endif
                        >
                            {{ $value->name }}
                        </option>
                    @endforeach
                </select>
            </div>
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
                        value="{{ $value->id }}"
                        @if ($data->branch_id == $value->id)) selected @endif
                    >
                      {{ $value->name_km }} ({{ $value->short_name }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('កម្មវត្ថុ') }}<span style='color: red'>*</span></label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('purpose') ? ' has-danger' : '' }}">
                <textarea
                    id="purpose"
                    class="form-control{{ $errors->has('purpose') ? ' is-invalid' : '' }}"
                    name="purpose"
                    required
                >{{ $data->purpose }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('មូលហេតុ') }}<span style='color: red'>*</span></label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('reason') ? ' has-danger' : '' }}">
                <textarea
                    id="reason"
                    class="form-control{{ $errors->has('reason') ? ' is-invalid' : '' }}"
                    name="reason"
                    required
                >{{ $data->reason }}
                </textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('បរិយាយ') }}<span style='color: red'>*</span></label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">
                <textarea
                        rows="4" 
                        id="desc"
                        class="form-control desc_textarea"
                        name="desc"
                        required
                >{!! $data->desc !!}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('កំណត់សម្គាល់') }}</label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('remark') ? ' has-danger' : '' }}">
                <textarea
                        id="remark"
                        class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"
                        name="remark"
                >{{ $data->remark }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
                <select required class="form-control select2 request-by-select2" name="user_id">
                    @foreach($requester as $item)
                        @if($item->id == $data->user_id)
                            <option selected value="{{ $item->id }}">{{ $item->name. ' ('.@$item->position->name_km.')' }} </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ៖') }}</label>
        <div class="col-sm-10">
            <div class="form-group{{ $errors->has('position_id') ? ' has-danger' : '' }}">
                <select class="form-control select2" name="reviewers[]" multiple="multiple">

                    @foreach($data->reviewers() as $item)
                        <option value="{{ $item->id }}" selected="selected">
                            {{ $item->name }}({{$item->position_name}})
                        </option>
                    @endforeach

                    @foreach($reviewer as $key => $value)
                        <option value="{{ $value->id }}">{{  $value->reviewer_name }}</option>
                    @endforeach

                </select>
            </div>
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
        <label class="col-sm-2 col-form-label">
            អនុម័តដោយ
            <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
               title="សម្រាប់ MMI នឹងទៅដល់ President Approver ដោយស្វ័យប្រវត្ត"
               data-placement="top">
            </i>
            <span style='color: red'>*</span>
        </label>
        <div class="col-sm-10">
            <div class="form-group">
                <select required class="form-control select2 request-by-select2" name="approver_id">
                    @foreach($approver as $item)
                        <option value="{{ @$item->id }}" 
                                @if($item->id == @$data->approver()->id) 
                                    selected 
                                @endif
                        >
                            {{ @$item->approver_name }}
                        </option>
                    @endforeach
                </select>
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
            formaction="{{ route('general_request.update', $data->id) }}"
            name="resubmit"
            value="1"
            form="requestForm"
            class="btn btn-info">
            {{ __('Re-Submit') }}
        </button>
    @else
        <button
            @if (
                ($data->user_id != \Illuminate\Support\Facades\Auth::id()) && 
                (!in_array(config('app.type_general_request'), (array)Auth::user()->edit_pending_request))
            )
                disabled
                title="Only requester that able to edit the request"
            @endif
            type="submit"
            formaction="{{ route('general_request.update', $data->id) }}"
            name="submit"
            value="1"
            form="requestForm"
            class="btn btn-success">
            {{ __('Update') }}
        </button>
    @endif
</div>

<script type="text/javascript">
  
      //packing
      calculateAmountPacking();

      function calculateAmountPacking() {
          $('#sections_packing').on('change keyup mouseover click', '.qty_packing, .amount_packing, .currency_packing, .remove_packing', function() {

              // Total
              var totalKHR = 0;
              var totalUSD = 0;
              $('#sections_packing').find('.amount_packing').each(function() {
                  var currency = $(this).parent().parent().find('.currency_packing').val();
                  if(currency == 'KHR') {
                      totalKHR +=  parseFloat($(this).val());
                  } else if(currency == 'USD') {
                      totalUSD +=  parseFloat($(this).val());
                  } else {
                      // totalKHR +=  parseFloat($(this).val());
                  }
                  //console.log(parseFloat($(this).val()));
              }).end();

              $('#total_packing').text((formatMoney(totalUSD)));
              $('#total_input_packing').val((totalUSD));
              $('#totalKHR_packing').text((formatMoney(totalKHR)));
              $('#total_khr_input_packing').val((totalKHR));
          });
      }


      //define template
      var template_packing = $('#sections_packing .section_packing:first').clone();

      //define counter
      var sectionsCount_packing = 1;

      //add new section
      $('body').on('click', '.addsection_packing', function() {

          //increment
          sectionsCount_packing++;

          //loop through each input
          var sections_packing = template_packing.clone().find(':input').each(function(){
              
              //set id to store the updated section number
              var newId = this.id + sectionsCount_packing;

              //update for label
              $(this).prev().attr('for', newId);

              //update id
              this.id = newId;
              this.value = '';
              if ($(this).hasClass('qty')) {
                  this.value = 1;
              }
              if ($(this).hasClass('amount')) {
                  this.value = 0;
              }

          }).end()

          //inject new section
          .insertBefore('#add_more_packing');
          return false;
      });

      //add value
      $('body').on('click', '.addValue_packing', function() {
          $('.qty').val(1);
      });

      //remove section
      $('#sections_packing').on('click', '.remove_packing', function() {
          //fade out section
          if ($('.remove_packing').length > 1) {
              $(this).parent().fadeOut(300, function() {
                  $(this).parent().empty();
              });
          }
      });
</script>
