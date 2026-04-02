@push('js')

  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  
  <script>
      $(document).ready(function(){

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
            
          $(".select2").select2({
              placeholder: {
                  id: null, // the value of the option
                  text: ' << ជ្រើសរើស >> '
              }
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

          showType();

          $("#type").on('change load', function(event) {
              showType();
          });

          function showType(){

              var type = $('#type').val();
              if (type == 'request') {
                  $('.request').removeAttr('hidden', true);
                  $('.type_request').attr('required','required');

                  $('.report').attr('hidden','hidden');
                  $('.type_report').removeAttr('required', true);
              }
              else if (type == 'report') {
                  $('.report').removeAttr('hidden', true);
                  $('.type_report').attr('required','required');

                  $('.request').attr('hidden','hidden');
                  $('.type_request').removeAttr('required', true);
              }

          }

      });
  </script>

@endpush