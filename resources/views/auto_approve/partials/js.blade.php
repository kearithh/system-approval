@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function(){

      $(".select2").select2({
          // tags: true
      });

      $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight:true,
        autoclose: true
      });

      $('.datepicker').inputmask()

      @if(session('status')==1)
        Swal.fire({
          title: 'Auto Approve Success',
          icon: 'success',
          timer: '5000',
        })
      @elseif(session('status')==2)
        Swal.fire({
          title: 'Update Success',
          icon: 'success',
          timer: '5000',
        })
      @elseif(session('status')==3)
        Swal.fire({
          title: 'Delete Success',
          icon: 'success',
          timer: '5000',
        })
      @elseif(session('status')==4)
        Swal.fire({
          title: 'Please Try agian',
          icon: 'error',
          timer: '5000',
        })
      @endif

      $("#requestForm").submit( function(submitEvent) {
         $("#submit").attr('disabled','disabled');
         $('#divMsg').show();
     });

    });
  </script>

@endpush
