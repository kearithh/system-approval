<?php $authId = \Illuminate\Support\Facades\Auth::id(); ?>
@if (in_array($authId, (array)json_decode($object->viewable)))
    <a href="/{{ $uri }}/{{ $object->id }}/show?menu={{ request()->segment(1) }}&type={{ @$_GET['type'] }}" class="btn btn-xs btn-info preview" title="View the request">
        <i class="fa fa-eye"></i>
    </a>
@endif

@if (in_array($authId, (array)json_decode($object->editable)))
{{--    <a href="/{{ $uri }}/{{ $object->id }}/edit"  class="btn btn-xs btn-success" title="edit the request">--}}
{{--        <i class="fa fa-pen"></i>--}}
{{--    </a>--}}
    <button
        class="btn btn-xs btn-success edit-btn"
        title="Edit the request"
        data-id="{{ $object->id }}"
    >
        <i class="fa fa-pen"></i>
    </button>
@else
    <button
        disabled
        class="btn btn-xs btn-success"
        title="Edit the request"
    >
        <i class="fa fa-pen"></i>
    </button>
@endif

@if (in_array($authId, (array)json_decode($object->deletable)))
{{--    <form action="">--}}
            <button
                action="/{{ $uri }}/{{ $object->id }}/delete"
                method="POST"
                class="btn btn-xs btn-danger btn-delete"
                data-item-id="{{ $object->id }}"
                title="Delete request form">
                <i class="fa fa-trash"></i>
            </button>
{{--    </form>--}}
@else
    <button
        disabled
        class="btn btn-xs btn-danger"
        title="Delete request form"
    >
        <i class="fa fa-trash"></i>
    </button>
@endif