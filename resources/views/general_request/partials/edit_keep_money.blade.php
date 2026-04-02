<div class="card-body ">
  
    <div class="table-responsive">
        <table id="sections_keep" class="table table-hover" style="display: block; overflow-y: auto">
            <thead class="card-header ">
                <th style="width: 70px;">សកម្ម</th>
                <th style="min-width: 350px">ប្រភេទ<span style='color: red'>*</span></th>
                <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
                <th style="min-width: 120px">សាច់ប្រាក់អតិបរិមាដែលអាចរក្សារទុកបាន<span style='color: red'>*</span></th>
                <th style="min-width: 120px">សាច់ប្រាក់ជាក់ស្ដែង<span style='color: red'>*</span></th>
                <th style="min-width: 120px">សាច់ប្រាក់លើស<span style='color: red'>*</span></th>
            </thead>
            
            <tbody>
                @foreach($data->items as $key => $item)
                    <tr class="section_keep">
                        <td class="text-center">
                           <button type="button"
                                   id="remove_keep"
                                   class="remove_keep btn btn-sm btn-danger"
                           >
                               <i class="fa fa-trash"></i>
                           </button>
                        </td>
                        <td>
                            <input
                                required
                                class="col-sm-12 name_keep"
                                type="text"
                                value="{{ $item->name }}"
                                name="name_keep[]"
                            >
                        </td>
                        <td>
                            <select required class="col-sm-12 currency_keep" style="height: 30px" name="currency_keep[]">
                                <option value="">----</option>
                                <option value="KHR" @if($item->currency=='KHR') selected @endif >រៀល</option>
                                <option value="USD" @if($item->currency=='USD') selected @endif >ដុល្លារ</option>
                            </select>
                        </td>
                        <td>
                            <input
                                required
                                class="min_money col"
                                min="1"
                                type="number"
                                value="{{ $item->min_money }}"
                                name="min_money[]"
                            >
                        </td>
                        <td>
                            <input
                                required
                                class="col current_money"
                                type="number"
                                name="current_money[]"
                                step="0.1"
                                value="{{ $item->current_money }}" 
                            >
                        </td>
                        <td>
                            <input
                                required
                                class="excess_money col"
                                min="0"
                                type="number"
                                name="excess_money[]"
                                value="{{ $item->excess_money }}"
                            >
                        </td>
                    </tr>
                @endforeach
                <tr id="add_more_keep">
                    <td class="text-center">
                        <button type="button"
                                id="addItem_keep"
                                class="addsection_keep btn btn-sm btn-success"
                        >
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td colspan="5"></td>
                </tr>
                <tr style="background: #dee2e6">
                    <td colspan="4" class="text-right">សរុប៖</td>
                    <td>
                        $ <strong id="total_keep">{{ $data->total_amount_usd }}</strong>
                        <input type="hidden" name="total_keep" {{ $data->total_amount_usd }} id="total_input_keep">
                    </td>
                    <td>
                        <strong id="totalKHR_keep">{{ $data->total_amount_khr }}</strong><sup>៛</sup>
                        <input type="hidden" name="total_khr_keep" value="{{ $data->total_amount_khr }}" id="total_khr_input_keep">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


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
                            {{ $item->name }}({{ $item->position_name }})
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
            formaction="{{ route('general_request.update_keep_money', $data->id) }}"
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
            formaction="{{ route('general_request.update_keep_money', $data->id) }}"
            name="submit"
            value="1"
            form="requestForm"
            class="btn btn-success">
            {{ __('Update') }}
        </button>
    @endif
</div>

<script type="text/javascript">
  
    //keep_money
    calculateAmounKeep();

    function calculateAmounKeep() {
        $('#sections_keep').on('change keyup mouseover click', '.qty_keep, .min_money, .current_money, .excess_money, .currency_keep, .remove_keep', function() {

            // Total
            var totalKHR = 0;
            var totalUSD = 0;
            $('#sections_keep').find('.excess_money').each(function() {
                var currency = $(this).parent().parent().find('.currency_keep').val();
                if(currency == 'KHR') {
                    totalKHR +=  parseFloat($(this).val());
                } else if(currency == 'USD') {
                    totalUSD +=  parseFloat($(this).val());
                } else {
                    // totalKHR +=  parseFloat($(this).val());
                }
                //console.log(parseFloat($(this).val()));
            }).end();

            $('#total_keep').text((formatMoney(totalUSD)));
            $('#total_input_keep').val((totalUSD));
            $('#totalKHR_keep').text((formatMoney(totalKHR)));
            $('#total_khr_input_keep').val((totalKHR));
        });
    }


    //define template
    var template_keep = $('#sections_keep .section_keep:first').clone();

    //define counter
    var sectionsCount_keep = 1;

    //add new section
    $('body').on('click', '.addsection_keep', function() {

        //increment
        sectionsCount_keep++;

        console.log(1);

        //loop through each input
        var section_keep = template_keep.clone().find(':input').each(function(){

            //set id to store the updated section number
            var newId = this.id + sectionsCount_keep;

            //update for label
            $(this).prev().attr('for', newId);

            //update id
            this.id = newId;
            this.value = '';
            if ($(this).hasClass('min_money')) {
                this.value = 0;
            }
            if ($(this).hasClass('current_money')) {
                this.value = 0;
            }
            if ($(this).hasClass('excess_money')) {
                this.value = 0;
            }
        }).end()

        //inject new section
        .insertBefore('#add_more_keep');
        return false;
    });

    //add value
    $('body').on('click', '.addValue_keep', function() {
        $('.qty_keep').val(1);
    });

    //remove section
    $('#sections_keep').on('click', '.remove_keep', function() {
        //fade out section
        if ($('.remove_keep').length > 1) {
            $(this).parent().fadeOut(300, function() {
                $(this).parent().empty();
            });
        }
    });

</script>
