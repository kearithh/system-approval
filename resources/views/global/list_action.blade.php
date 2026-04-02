<a href="/{{ $uri }}/{{ $object->id }}/show?menu={{ request()->segment(1) }}&type={{ @$_GET['type'] }}" class="preview btn btn-xs btn-info" title="View the request">
    <i class="fa fa-eye"></i>
</a>
@if(can_action($object, @$type))
    <a href="/{{ $uri }}/{{ $object->id }}/edit" class="preview btn btn-xs btn-success" title="edit the request">
        <i class="fa fa-pen"></i>
    </a>
    {{--    <form class="delete-form" action="/{{ $uri }}/{{ $object->id }}/delete" method="POST" style="display: inline-block">--}}
    <button
        action="/{{ $uri }}/{{ $object->id }}/delete"
        method="POST"
        class="btn btn-xs btn-danger btn-delete"
        title="Delete request form">
        <i class="fa fa-trash"></i>
    </button>
    {{--    </form>--}}
@else
    <button class="btn btn-xs btn-success" disabled="true" title="edit the request">
        <i class="fa fa-pen"></i>
    </button>
    <button class="btn btn-xs btn-danger btn-delete" disabled title="Delete request form">
        <i class="fa fa-trash"></i>
    </button>
@endif

{{--@include('global.list_action', ['uri' => 'request_hr', 'object' => $object])--}}
