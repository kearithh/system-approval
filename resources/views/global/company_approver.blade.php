<select required class="form-control select2"​​​​​ id="approver" required name="approver">
    <option value="{{ null }}"> << ជ្រើសរើស >> </option>
    @foreach(@$approvers as $item)
		<option value="{{ @$item->id }}">{{ @$item->name }}</option>
    @endforeach
</select><br/>