@push('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function(){
      //var va = ($('#sort_review').text());

      $(".select2").select2({
          // tags: true
      });

      $(".select_tags").select2({
        tags: true
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

        $old_position = $('option:selected', this).data('position');
        $old_department = $('option:selected', this).data('department');
        $old_branch = $('option:selected', this).data('branch');

        $("#old_position").val($old_position).trigger('change');
        $("#old_department").val($old_department).trigger('change');
        $("#old_branch").val($old_branch).trigger('change');

      });

      $('#type').on('change', function () {

        var title = $("#type option:selected").text();
        $("#title").val(title);
        $("#title_staff").text(title + 'ឈ្មោះ');

        var type = $(this).val();
        if( type == 3 ){
          $('#new_position').removeAttr('required');
          $('#new_branch').removeAttr('required');
          $('#new').attr('hidden', true);
          $("#old_posi").text('តួនាទីជា');
          $('#time').attr('hidden', true);
          $('#time_schedule').attr('hidden', true);
          $('#working_day').attr('hidden', true);
          $("#old_salary").text('ប្រាក់បៀរវត្សរ៍គោល');
        }
        else if( type == 6 ){
          $('#new_position').removeAttr('required');
          $('#new_branch').removeAttr('required');
          $('#new').removeAttr('hidden');
          $('#time_schedule').removeAttr('hidden');
          $('#working_day').removeAttr('hidden');
          $("#new_salary").text('ប្រាក់បៀរវត្សថ្មី');
          $("#old_salary").text('ប្រាក់បៀរវត្សរ៍គោល');
        }
        else{
          $('#new_position').attr('required', true);
          $('#new_branch').attr('required', true);
          $('#new').removeAttr('hidden');
          $("#old_posi").text('តួនាទីបច្ចុប្បន្ន');

          $("#new_salary").text('ប្រាក់បៀរវត្សថ្មី');
          $("#old_salary").text('ប្រាក់បៀរវត្សរ៍គោល');
          $('#time_schedule').attr('hidden', true);
          $('#working_day').attr('hidden', true);
          $('#time').attr('hidden', true);
          if( type == 0 ){
            $('#time').removeAttr('hidden');
            $("#new_salary").text('ប្រាក់ម៉ោងថ្មី');
            $("#old_salary").text('ប្រាក់ម៉ោងបច្ចុប្បន្ន');
          }
        }
      });


      $('#old_branch').on('change', function () {

        var branch = $(this).val();

        if( branch == 1 ){
          $('.old_department').removeAttr('hidden');
        }
        else{
          $('.old_department').attr('hidden', true);
        }

      });


      $('#new_branch').on('change', function () {

        var branch = $(this).val();

        if( branch == 1 ){
          $('.new_department').removeAttr('hidden');
        }
        else{
          $('.new_department').attr('hidden', true);
        }

      });


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
          ['height', ['height']],
          ['fontname', ['fontname']],
        ]
      });
      $('.note-popover').attr('hidden',true);
    });
  </script>

@endpush
