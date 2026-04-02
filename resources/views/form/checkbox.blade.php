{{--<div class="row">--}}
{{--    <label class="col-sm-4 col-form-label text-right">{{ isset($label) ? $label : $data->name }}</label>--}}
{{--    <div class="col-sm-8">--}}
{{--        <div class="form-group{{ $errors->has($data->name) ? ' has-danger' : '' }}">--}}
{{--            <input--}}
{{--                @foreach ($data as $key => $attr)--}}
{{--                    {!! $key !!} ="{!! $attr !!}"--}}
{{--                @endforeach--}}

{{--                @if($class)--}}
{{--                    class = "form-control {!!  $errors->has($data->name) ? 'is-invalid' : '' !!}"--}}
{{--                @endif--}}
{{--            />--}}
{{--            @if ($errors->has($data->name))--}}
{{--                <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first($data->name) }}</span>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="row">
    <label class="col-sm-2 col-form-label text-right">{{ isset($label) ? $label : $data->name }}</label>
    <div class="col-sm-10">
        @foreach($data['item'] as $key => $item)
            <div class="form-check form-check-inline">
                <input
                    type="checkbox"
                    name="{{ $data['name'] }}"
                    @if(@$class)
                    class = "form-check-input {{ @$item['class'] }}  {!!  $errors->has(@$data['name']) ? 'is-invalid' : '' !!}"
                @endif
                @foreach ($item as $key => $attr)
                    {!! $key !!} ="{!! $attr !!}"
                @endforeach
                >
                <label class="form-check-label " for="{{ @$item['id']  }}">{{ @$item['label'] }}</label>
            </div>
        @endforeach
    </div>
</div>

