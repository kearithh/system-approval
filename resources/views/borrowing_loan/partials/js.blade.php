@push('js')
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script> -->
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function(){

      $(".select2").select2({
          // tags: true
      });

      $(".select_tags").select2({
        tags: true
      });

      $('#approve_by').select2();

      $("select").on("select2:select", function (evt) {
          var element = evt.params.data.element;
          var $element = $(element);

          $element.detach();
          $(this).append($element);
          $(this).trigger("change");
      });

      $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight:true,
        autoclose: true
      });

      $('.datepicker').inputmask()

      @if(session('status')==1)
        Swal.fire({
          title: 'Insert Success',
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

      // check file upload
      $("#requestForm").submit( function(submitEvent) {
          $("#error").remove();
          $("#file").css("color", "black");
          var fileInput = $("#file")[0].files[0];
          if (fileInput) {
              if (fileInput.size > 15500000 || fileInput.fileSize > 15500000) { // file not biger 15M
                  $("#file").css("color", "red");
                  $("#validate").after('<span id="error" style="color:red"> ឯកសារមានទំហំលើស 15M <br></span>');
                  submitEvent.preventDefault();
              }
          }
      });

      $("#file").change( function() {
          $("#error").remove();
          $("#file").css("color", "black");
      });

      $( "#back" ).on( "click", function( event ) {
          if(localStorage.previous){
              window.location.href = localStorage.previous;
              window.localStorage.removeItem('previous');
          }
          else{
              alert("Can't previous");
          }
      });

      formartnumber();

      $(".money").on('change keyup mouseover load', function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;
        formartnumber();
      });

      function formartnumber(){
        // format number
          $('.money').val(function(index, value) {
            return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            ;
          });
      }

    });
  </script>

@endpush
