@extends('layouts.show_a4')
@section('content')
    <div id="action_container" style="width: 1024px; margin: auto;background: white;">
        @include('group_request.partials.approve_action', ['object' => $data])
        <br>
        <br>
        @include('group_request.partials.reviewer_table', ['reviewers' => $data->getReviewersByRequestId()->push($data->getApproverByRequestId())])
    </div>
    <div style="width: 1024px; margin: auto;background: white; min-height: 1355px;">
        <iframe src="{{ asset('/'.@$data->attachments[0]['src']) }}#view=FitH" width="100%" height="1355px"></iframe>
        @include('global.comment_modal', ['route' =>route('re.item-reject', $data->id)])
    </div>

{{--    <input id="file_upload" type="file" onchange="previewFile()"><br>--}}
{{--    <img id="img_preview" src="" height="200" alt="Image preview...">--}}
@endsection

@push('js')
    <script>

        // function previewFile() {
        //     const preview = document.querySelector('#img_preview');
        //     alert(preview);
        //     const file = document.querySelector('#file_upload').files[0];
        //     const reader = new FileReader();
        //
        //     reader.addEventListener("load", function () {
        //         preview.src = reader.result;
        //         window.localStorage.file = reader.result;
        //     }, false);
        //
        //     if (file) {
        //         reader.readAsDataURL(file);
        //     }
        // }


        approve();
        reject();

        function approve()
        {
            $( "#approve_btn" ).on( "click", function( event ) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {
                        $('#action_approve').submit();
                    }
                })
            });
        }

        function reject()
        {
            $( "#reject_btn" ).on( "click", function( event ) {
                event.preventDefault();
                $('#comment_modal').modal('show');
            });
        }

        $( "#back" ).on( "click", function( event ) {
            window.location.href = "<?php echo session('back_btn');?>";
        });


    </script>
@endpush
