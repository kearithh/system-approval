<div class="row">
    <label class="col-sm-2 col-form-label">{{ isset($label) ? $label : $data->name }}</label>
    <div class="col-sm-10">
        <div class="form-group{{ $errors->has($data->name) ? ' has-danger' : '' }}">
            <textarea
                @foreach ($data as $key => $attr)
                    {!! $key !!} ="{!! $attr !!}"
                @endforeach
                class = "form-control @if($class){!!  $errors->has($data->name) ? 'is-invalid' : '' !!}  @endif"
            >@if($value){{ @$value }}@else{{ old($data->name) }}@endif</textarea>

            @if ($errors->has($data->name))
                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first($data->name) }}</span>
            @endif
        </div>
    </div>
</div>
