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


      $('.datepicker').datepicker({
          format: 'dd-mm-yyyy',
          todayHighlight:true,
          autoclose: true
      });

      $(".select2").select2({
          // tags: true
      });

      $(".select2").on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
      });

      // $(".select2").select2("container").find("ul.select2-choices").sortable({
      //     containment: 'parent',
      //     start: function() { $(".select2").select2("onSortStart"); },
      //     update: function() { $(".select2").select2("onSortEnd"); }
      // });

      $( "#back" ).on( "click", function( event ) {
          if(localStorage.previous){
              window.location.href = localStorage.previous;
              window.localStorage.removeItem('previous');
          }
          else{
              alert("Can't previous");
          }
      });

      // $(".select2").on("select2:select", function (evt) {
      //   var element = evt.params.data.element;
      //   var $element = $(element);
      //   $element.detach();
      //   $(this).append($element);
      //   $(this).trigger("change");
      // });

      $("#type").on('change', function () {
        var type = $(this).val();
        console.log(type);

        if(type == 'សេចក្តីណែនាំ') {
          $('.apply').attr('style', 'display:block');
          $('#for').attr('required',true);

          $('#practise').attr('style', 'display:block');
          $('#practise_point').attr('required',true);

          $('#hr_request').attr('style', 'display:none');
          $('#hr').removeAttr('required');

          $value = "all";

        } else if (type == 'សេចក្តីសម្រេច' || type == 'សេចក្តីជូនដំណឹង') {
          $('.apply').attr('style', 'display:none');
          $('#for').removeAttr('required');

          $('#practise').attr('style', 'display:block');
          $('#practise_point').attr('required',true);

          $('#hr_request').attr('style', 'display:none');
          $('#hr').removeAttr('required');

          $value = "ceo";

        } else {
          $('.apply').attr('style', 'display:none');
          $('#for').removeAttr('required');

          $('#practise').attr('style', 'display:none');
          $('#practise_point').removeAttr('required');

          $('#hr_request').attr('style', 'display:block');
          $('#hr').attr('required',true);

          $value = "all";

        }

        find_approver($value);

      });


      $("#company_id").on('change', function () {
        var type = $(this).val();
        // $data = $(this).data('group_request');
        var option = $('option:selected', this).data('reference');
        $("#reference").html(option);
        $("#reference-value").html(option);
        // alert(type);
      });

      function find_approver($value){
          $.ajax({
            type : 'get',
            url : "{{URL::route('request_memo.find_approver')}}",
            data:{'type':$value},
            success:function(data){
              //alert(data);
              $(".approver").empty();
              $(".approver").html(data);
            }
          });
      };


      $('.point_textarea, .desc_textarea').summernote({
          fontNames: [
              "Khmer OS Content",
              "Khmer OS Muol Light"
          ],
          toolbar: [
              // [groupName, [list of button]]
              ['style', ['bold', 'italic', 'underline', 'clear']],
              // ['font', ['strikethrough', 'superscript', 'subscript']],
              // ['fontsize', ['fontsize']],
              // ['color', ['color']],
              ['para', ['ul', 'paragraph']],
              // ['height', ['height']]
              ['fontname', ['fontname']],
              ['height', ['height']]
          ],
      });

      $('.note-popover').attr('hidden',true);

      $('#addPoint').on('click', function () {
          // var checkVal = $(".hidden").first().children().children().html();
          // alert(checkVal);
          var pointBox = $('div.point').each(function() {
              var hiddenBox = $(this).hasClass('hidden');
              if (hiddenBox) {
                  $(this).removeClass('hidden');
                  return false
              }
          });
      });
      $('#start_date').inputmask()
    });
  </script>
@endpush
