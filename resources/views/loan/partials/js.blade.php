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

      showProfile();

      $("#company").on('change load', function(event) {
        showProfile();
      });

      function showProfile(){

        var company_id = $('#company').val();
        if (company_id == 2) { // MFI
          $('#profile').removeAttr('hidden', true);

          $('#en_name').attr('required','required');
          $('#dob').attr('required','required');
          $('#id_types').attr('required','required').trigger('change.select2');
          // $('#nid').attr('required','required');

          $('#mfi_service').removeAttr('hidden', true);
          $('#ngo_service').attr('hidden','hidden');
          $('.mfi_service_input').attr('required','required');
          $('.ngo_service_input').removeAttr('required');
        }
        else {
          $('#profile').attr('hidden','hidden');

          $('#en_name').removeAttr('required');
          $('#dob').removeAttr('required');
          $('#id_types').removeAttr('required').trigger('change.select2');
          // $('#nid').removeAttr('required');

          $('#mfi_service').attr('hidden','hidden');
          $('#ngo_service').removeAttr('hidden', true);
          $('.mfi_service_input').removeAttr('required');
          $('.ngo_service_input').attr('required','required');
        }

      }

      formartnumber();

      $("#money").on('change keyup mouseover load', function(event) {
        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;
        formartnumber();
      });

      function formartnumber(){
        // format number
          $('#money').val(function(index, value) {
            return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            ;
          });
          // todo: need recheck about condition for approver
          //groupPosition();
      }

      function groupPosition() {

        var amount = $('#money').val().replace(/,/g, '');
        var amountInt = parseInt(amount);

        // $("option", "#approve_by").attr('disabled','disabled').trigger('change');
        //'President', 'DCEO', 'HOO', 'RM', 'BM', 'DBM', 'DOM'

        $('#approve_by').select2('destroy');
        $('#approve_by').select2();

        if(amountInt <= 4000000 ){
          $("option", "#approve_by").removeAttr('disabled', true).trigger('change.select2');
          $("option[data-position_short='BM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DBM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DOM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DCEO']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='President']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          //console.log('small');
        }
        else if(amountInt <= 6000000 ){
          $("option", "#approve_by").removeAttr('disabled', true).trigger('change.select2');
          $("option[data-position_short='BM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DBM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DCEO']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='President']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          //console.log('small');
        }
        else if(amountInt > 6000000 ){
          $("option", "#approve_by").removeAttr('disabled', true).trigger('change.select2');
          $("option[data-position_short='BM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DBM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='RM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          $("option[data-position_short='DOM']", "#approve_by").attr('disabled','disabled').trigger('change.select2');
          console.log('big');
        }
        else{
          $("option", "#approve_by").removeAttr('disabled', true).trigger('change.select2');
          //console.log('not');
        }

        //var position = $('option:selected', '#approve_by').data('position_short');

        //$("#approve_by option[data-position_short='HOO']").attr('disabled','disabled').trigger('change');

        //$("option[data-position_short != 'HOO']", "#approve_by").attr('disabled','disabled').trigger('change');

        //console.log(position);

      };
    });

    //define template
    var template = $('#sections .section:first').clone();

    //define counter
    var sectionsCount = 1;

    //add new section
    $('body').on('click', '.addsection', function() {

        //increment
        sectionsCount++;
        // check length < 5
        if($('.remove').length < 5){
          //loop through each input
          var section = template.clone().find(':input').each(function(){
            this.value = '';
          }).end()

          //inject new section
          .insertBefore('#add_more');
        }
        return false;
    });

    //remove section
    $('#sections').on('click', '.remove', function() {
      //fade out section
      if($('.remove').length > 1){
        $(this).parent().fadeOut(300, function(){
          $(this).parent().empty();
        });
      }
    });

  </script>

@endpush
