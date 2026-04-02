<div class="reviewer">
    <div class="col-sm-12">
            <div class="row">
                <label class="col-sm-2 col-form-label">រៀបចំដោយ</label>
                <div class="col-sm-10">
                    <div class="form-group">
                        <select class ="select2 form-control" name="created_by" disabled="true">
                            @foreach($staffs as $key => $value)
                                <option
                                    value="{{ $value->id }}"
                                    @if($value->id == $memo->user_id) selected @endif >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-sm-12">
        <div class="row">
            <label class="col-sm-2 col-form-label">ពិនិត្យ និងបញ្ជូនបន្តដោយ៖<span style='color: red'>*</span></label>
            <div class="col-sm-10">
                <div class="form-group">
                    <select class ="select2 form-control" required="required" name="reviewers[]" multiple>
                            @foreach($reviewers as $item)
                                <option
                                    <?php $existReviewer = $memo->approvals()->pluck('reviewer_id')->toArray(); ?>
                                    @if(in_array($item->id, $existReviewer))
                                    selected
                                    @endif

                                    value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-2">
                 <label>អនុម័តដោយ<span style='color: #ff0000'>*</span></label>
            </div>
            <div class="col-md-10 form-group">
                <select class="form-control reviewer select2" readonly="true" name="approver" required>
                    @foreach($approver as $item)
                        <option value="{{ @$item->id }}" @if($item->id == @$memo->approver()->id) selected @endif>{{ @$item->name }}</option>
                    @endforeach
                </select><br/>
            </div>
        </div>
    </div>
</div>
