

@if($item->status == config('app.approved'))
    <button type="button" disabled class="btn btn-xs btn-default upload-file" title="Upload attachment">

        <i class="fa fa-upload"></i>
    </button>

    <button type="button" disabled class="btn btn-xs btn-info" title="View the attachment">

        <i class="fa fa-eye"></i>
    </button>

    <button type="button" disabled class="btn btn-xs btn-success" title="Approve the report">

        <i class="fa fa-check"></i>
    </button>



@else

    <button
        class="btn btn-xs btn-default upload-file"
        title="Upload attachment"
        data-id="{{ $item->id }}"
        >
        <i class="fa fa-upload"></i>
    </button>

    @if (is_null($item->attachments))
        <button disabled class="btn btn-xs btn-info" title="Preview file">
            <i class="fa fa-eye"></i>
        </button>
    @else
        <a href="{{ route('re.item.show', $item->id) }}" target="_self" class="btn btn-xs btn-info" title="View the attachment">
            <i class="fa fa-eye"></i>
        </a>
    @endif


{{--    @if ($item->user_id == Auth::id())--}}
{{--        <button disabled href="#" class="btn btn-xs btn-success" title="Approve the report">--}}
{{--            <i class="fa fa-check"></i>--}}
{{--        </button>--}}
{{--    @else--}}
        <a href="{{ route('re.item-approve', $item->id) }}" class="btn btn-xs btn-success" title="Approve the report">
            <i class="fa fa-check"></i>
        </a>
{{--    @endif--}}

@endif
