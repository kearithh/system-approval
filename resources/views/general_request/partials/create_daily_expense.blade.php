<div class="card-body ">
  
  <div class="table-responsive">
      <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
          <thead class="card-header ">
              <th style="width: 70px;">Action</th>
              <th style="min-width: 50px">Acc No<span style='color: red'>*</span></th>
              <th style="min-width: 200px">Account Name<span style='color: red'>*</span></th>
              <th style="min-width: 200px">Description<span style='color: red'>*</span></th>
              <th style="min-width: 100px">Currency<span style='color: red'>*</span></th>
              <th style="min-width: 70px">Debit<span style='color: red'>*</span></th>
              <th style="min-width: 70px">Credit<span style='color: red'>*</span></th>
          </thead>
          
          <tbody>
              <tr class="section">
                  <td class="text-center">
                     <button type="button"
                             id="remove"
                             class="remove btn btn-sm btn-danger"
                     >
                         <i class="fa fa-trash"></i>
                     </button>
                  </td>
                  <td>
                      <input
                          required
                          class="col-sm-12 no"
                          type="number"
                          name="no[]"
                      >
                  </td>
                  <td>
                      <input
                          required
                          class="col-sm-12 name"
                          type="text"
                          name="name[]"
                      >
                  </td>
                  <td>
                      <input
                          required
                          class="descrip col"
                          type="text"
                          name="descrip[]"
                      >
                  </td>
                  <td>
                      <select required class="col-sm-12 currency" style="height: 30px" name="currency[]">
                          <option value="">----</option>
                          <option value="KHR" selected >KHR</option>
                          <option value="USD">USD</option>
                      </select>
                  </td>
                  <td>
                      <input
                          required
                          class="debit col"
                          value="0"
                          min="0"
                          step="0.01"
                          type="number"
                          name="debit[]"
                      >
                  </td>
                  <td>
                      <input
                          required
                          class="col credit"
                          type="number"
                          name="credit[]"
                          step="0.01"
                          value="0" 
                      >
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
                  <td colspan="6"></td>
              </tr>
          </tbody>
      </table>
  </div>


  <div class="row">
      <label class="col-sm-2 col-form-label">{{ __('ឯកសារភ្ជាប់') }}</label>
      <div class="col-sm-10">
          <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
              <input
                  type="file"
                  id="file"
                  class="{{ $errors->has('file') ? ' is-invalid' : '' }}"
                  name="file[]"
                  multiple="multiple"
                  value="{{ old('file') }}"
              >
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
                          @if(Auth::user()->company_id == $value->id))
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
            @if(Auth::user()->branch_id == $value->id) selected @endif
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
        ></textarea>
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
              ></textarea>
          </div>
      </div>
  </div>

  <div class="row">
    <label class="col-sm-2 col-form-label">{{ __('ស្នើដោយ') }}</label>
    <div class="col-sm-10">
      <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }}">
        <select required class="form-control select2 request-by-select2" name="user_id">
          @foreach($requester as $item)
            @if($item->id==Auth::id())
                <option value="{{ $item->id}} " selected="selected">{{ $item->name. ' ('.@$item->position->name_km.')' }}</option>
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
          @foreach($reviewer as $item)
            <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-2">
      <label>
        ត្រួតពិនិត្យ(ហត្ថលេខាតូច)
        <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
             title="ផ្នែកពាក់ព័ន្ធដែលជួយត្រួតពិនិត្យ Short sign"
             data-placement="top"></i>
        </label>
    </div>
    <div class="col-md-10 form-group">
      <select class="form-control select2" name="review_short[]" multiple>
        @foreach($reviewer as $item)
          <option value="{{ $item->id }}">{{ $item->reviewer_name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row">
      <label class="col-sm-2 col-form-label">
          អនុម័តដោយ
          <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
             title="គណនេយ្យហិរញ្ញវត្ថុជាន់ខ្ពស់"
             data-placement="top">
          </i>
          <span style='color: red'>*</span>
      </label>
      <div class="col-sm-10">
          <div class="form-group">
              <select required class="form-control select2 request-by-select2" name="approver_id">
                  <option value=""><<ជ្រើសរើស>></option>
                  @foreach($approver as $item)
                      <option value="{{ @$item->id }}">
                          {{ @$item->approver_name }}
                      </option>
                  @endforeach
              </select>
          </div>
      </div>
  </div>

</div>
<div class="card-footer">
  <button
          type="submit"
          value="1"
          name="submit"
          formaction="{{ route('general_request.store_daily_expense')  }}"
          form="requestForm"
          class="btn btn-success">
    {{ __('Submit') }}
  </button>
</div>

<script type="text/javascript">

    //define template
    var template = $('#sections .section:first').clone();

    //define counter
    var sectionsCount = 1;

    //add new section
    $('body').on('click', '.addsection', function() {

        //increment
        sectionsCount++;

        console.log(1);

        //loop through each input
        var section = template.clone().find(':input').each(function(){

            //set id to store the updated section number
            var newId = this.id + sectionsCount;

            //update for label
            $(this).prev().attr('for', newId);

            //update id
            this.id = newId;
            this.value = '';
            if ($(this).hasClass('debit')) {
                this.value = 0;
            }
            if ($(this).hasClass('credit')) {
                this.value = 0;
            }
        }).end()

        //inject new section
        .insertBefore('#add_more');
        return false;
    });

    //add value
    $('body').on('click', '.addValue', function() {
        $('.qty').val(1);
    });

    //remove section
    $('#sections').on('click', '.remove', function() {
        //fade out section
        if ($('.remove').length > 1) {
            $(this).parent().fadeOut(300, function() {
                $(this).parent().empty();
            });
        }
    });

</script>
