<div class="reviewer">
    <div class="col-sm-12">

        <div class="row">
            <div class="col-md-2">
              <label>រៀបចំដោយ</label>
            </div>
            <div class="col-md-10">
                <select class="form-control select2"​​​​​ name="created_by">
                    @foreach($staffs as $key => $value)
                        @if($value->id==Auth::id())
                            <option value="{{ $value->id}} " selected="selected">{{ $value->name }} ({{ $value->position->name_km }})</option>
                        @endif
                    @endforeach()
                </select><br/>
            </div>
        </div>
    </div>
    <div class="col-sm-12 mt-3">
        <?= Form::select([
            "multiple" => "multiple",
            "class" => "select2",
            "label" => "ពិនិត្យ និងបញ្ជូនបន្តដោយ៖",
            "name" => "reviewers[]",
            "required" => "required",
            "option" => $reviewers->pluck('name', 'id')->toArray(),
        ]); ?>
    </div>
</div>
