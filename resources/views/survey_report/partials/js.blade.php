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

        $( "#back" ).on( "click", function( event ) {
            if(localStorage.previous){
                window.location.href = localStorage.previous;
                window.localStorage.removeItem('previous');
            }
            else{
                alert("Can't previous");
            }
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

        $('.point_textarea, .desc_textarea').summernote({
          placeholder: 'ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ',
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
        
        $(".amount").inputmask();

        formartnumber();
    
        $("#budget").on('change keyup mouseover load', function(event) {
          // skip for arrow keys
          if(event.which >= 37 && event.which <= 40) return;
          formartnumber();
        });

        function formartnumber(){
          // format number
            $('#budget').val(function(index, value) {
              return value
              .replace(/\D/g, "")
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
              ;
            });
        }
    });
    </script>
@endpush
