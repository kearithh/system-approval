<select required class="form-control select2"​​​​​ id="department_id" name="department_id">
    <option value="{{ null }}"> << ជ្រើសរើស >> </option>
    @foreach($data as $key => $value)
        @if($value->id == Auth::user()->department_id)
            <option value="{{ $value->id}} " selected="selected">{{ @$value->name_km }}</option>
        @else
            <option value="{{ $value->id}} ">{{ @$value->name_km }}</option>
        @endif
    @endforeach()
</select><br/>
