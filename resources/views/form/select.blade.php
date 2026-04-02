<div class="row">
    <label class="col-sm-2 col-form-label">{!! isset($label) ? $label : $data['name']  !!} </label>
    <div class="col-sm-10">
            <div class="form-group {{ $errors->has($data['name']) ? ' has-danger' : '' }}">
                <select
                    @foreach ($data as $key => $attr)
                        {!! $key !!} ="{!! $attr !!}"
                    @endforeach
                    class = "form-control  @if($class){!!  $errors->has($data['name']) ? 'is-invalid' : $class !!}@endif"
                >
                    @foreach($option as $key => $item)
                        <option
                            value="{{ isset($item['value']) ? $item['value'] : $key }}"
                            @if(isset($selected))
                                @if($key == $selected)
                                    selected
                                @endif
                            @endif
                        >
                            {{ isset($item['label']) ? $item['label'] : $item }}
                        </option>
                    @endforeach
                </select>
            </div>
    </div>
</div>
