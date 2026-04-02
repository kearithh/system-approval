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
{{--            <div class="col-sm-12">--}}
                <div class="row">
                    <label class="col-sm-2 col-form-label">ពិនិត្យ និងបញ្ជូនបន្តដោយ៖</label>
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
{{--            </div>--}}
    </div>
</div>
