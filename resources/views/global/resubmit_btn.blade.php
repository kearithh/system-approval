@if ($memo->status == config('app.approve_status_reject'))
    <button
        title="Update and Submit to Approve Again"
        type="submit"
        value="1"
        name="resubmit"
        class="btn btn-danger">
        {{ __('Resubmit') }}
    </button>
@endif
