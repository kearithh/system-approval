@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function(){

      $(".select2").select2({
        // tags: true
        placeholder: {
            id: null, // the value of the option
            text: ' << ជ្រើសរើស >> '
        }
      });

      $(".select_tags").select2({
        tags: true,
        placeholder: {
            id: null, // the value of the option
            text: ' << ជ្រើសរើស >> '
        }
      });

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

      check_type();

      $('#type').on('change', function () {
        check_type();
      });

      $('#company_id').on('change', function () {
        check_type();
      });

      function check_type(){
        var type = $('#type').val();
        var com = $('#company_id').val();
        var title = $("#type option:selected").text();
        $("#title").val(title);
        $("#title_staff").text(title + 'ឈ្មោះ');
        if(type == 2) { // type = approver resign 

          $('.request_resign_show').attr('style', 'display:none');
          $('.request_resign').removeAttr('required');

          $('.approver_resign_show').attr('style', 'display:block');
          $('.approver_resign').attr('required', true);

          if(com == 2 || com == 3 || com == 14) { //and company mfi or ngo
            $('.approver_resign_show_obj').attr('style', 'display:block');
            $('.approver_resign_obj').attr('required', true);
          }
          else {
            $('.approver_resign_show_obj').attr('style', 'display:none');
            $('.approver_resign_obj').removeAttr('required');
          }
        }
        else {
          $('.request_resign_show').attr('style', 'display:block');
          $('.request_resign').attr('required', true);

          $('.approver_resign_show').attr('style', 'display:none');
          $('.approver_resign').removeAttr('required');

          $('.approver_resign_show_obj').attr('style', 'display:none');
          $('.approver_resign_obj').removeAttr('required');
        }
        check_contract();
      }

      $("#is_contract").on('change', function () {
        check_contract();
      });

      function check_contract(){
        var contract = $('#is_contract').val();
        if(contract == 2) {
            $('.no_contract').attr('style', 'display:block;');
            $('#contract').attr('required',true);
        } else {
            $('.no_contract').attr('style', 'display:none;');
            $('#contract').removeAttr('required');
        }
      }

      $( "#back" ).on( "click", function( event ) {
          if(localStorage.previous){
              window.location.href = localStorage.previous;
              window.localStorage.removeItem('previous');
          }
          else{
              alert("Can't previous");
          }
      });

      $('#staff').on('change', function () {

        $position = $('option:selected', this).data('position');
        if ($position){
          $("#position").val($position).trigger('change');
        }

        $department = $('option:selected', this).data('department');
        if ($department){
          $("#department").val($department).trigger('change');
        }

        $branch = $('option:selected', this).data('branch');
        if ($branch){
          $("#branch").val($branch).trigger('change');
        }

        // $("#position").val($position).trigger('change');
        // $("#department").val($department).trigger('change');
        // $("#branch").val($branch).trigger('change');

      });


      // $('#branch').on('change', function () {

      //   var branch = $(this).val();

      //   if( branch == 1 ){
      //     $('.department').removeAttr('hidden');
      //   }
      //   else{
      //     $('.department').attr('hidden', true);
      //   }

      // });


      $('.point_textarea, .desc_textarea').summernote({
        fontNames: [
          // "Roboto",
          // "Arial",
          // "Arial Black",
          // "Comic Sans MS",
          // "Courier New",
          // "Helvetica Neue",
          // "Helvetica",
          // "Impact",
          // "Lucida Grande",
          // "Tahoma",
          // "Times New Roman",
          // "Verdana",
          "Khmer OS Content",
          "Khmer OS Muol Light"
        ],
        toolbar: [
          // [groupName, [list of button]]
          ['style', ['bold', 'italic', 'underline', 'clear']],
          // ['font', ['strikethrough', 'superscript', 'subscript']],
          // ['fontsize', ['fontsize']],
          // ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          // ['height', ['height']]
          ['fontname', ['fontname']],
        ]
      });
    });
  </script>

@endpush
