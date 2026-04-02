@if (Session::get('status') == 1)
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'The request has been success',
            icon: 'success',
            timer: '2000',
        })
    </script>
@endif
@if (Session::get('status') == -1)
    <script>
        Swal.fire({
            title: 'Fail!',
            text: "{{ Session::get('message') }}",
            icon: 'error',
        })
    </script>
@endif
@if (Session::get('status') == -2)
    <script>
        Swal.fire({
            title: 'Fail!',
            text: 'The request has been deleted',
            icon: 'error',
        })
    </script>
@endif
