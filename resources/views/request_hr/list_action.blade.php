<a href="/{{ $uri }}/{{ $object->id }}/show" class="btn btn-xs btn-info" title="View the request">
    <i class="fa fa-eye"></i>
</a>
@if(
    $object->status == config('app.approve_status_reject')
    || \App\RequestHR::isFirstApprove($object->id) == 0
    || \Illuminate\Support\Facades\Auth::user()->role == config('app.system_admin_role')
)
    <a href="/{{ $uri }}/{{ $object->id }}/edit" class="btn btn-xs btn-success" title="edit the request">
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
