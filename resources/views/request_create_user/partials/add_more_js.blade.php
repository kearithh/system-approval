@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        $(document).ready(function(){
            $(".select2").select2({
                // tags: true,
                placeholder: {
                    id: null, // the value of the option
                    text: ' << ជ្រើសរើស >> '
                }
            });

            $(".select_tag").select2({
                tags: true,
                placeholder: {
                    id: null, // the value of the option
                    text: ' << ជ្រើសរើស >> '
                }
            });

            // $("select").on("select2:select", function (evt) {
            //     var element = evt.params.data.element;
            //     var $element = $(element);

            //     $element.detach();
            //     $(this).append($element);
            //     $(this).trigger("change");
            // });

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

            $(document).on('click', '.append-datepicker', function(){
              $(this).datepicker({
                format: 'dd-mm-yyyy',
                todayHighlight:true,
                autoclose: true
              }).focus();
            });

            showType();

            $("#company").on('change load', function(event) {
                showType();
            });

            function showType(){

                var company_id = $('#company').val();
                if (company_id == 6) { // MMI
                    $('#request_mmi').removeAttr('hidden', true);
                    $('#type_mmi').attr('required','required');

                    $('#request_all').attr('hidden','hidden');
                    $('#type_all').removeAttr('required', true);

                    $('#request_skp').attr('hidden','hidden');
                    $('#type_skp').removeAttr('required', true);
                }
                else if (company_id == 1 || company_id == 2 || company_id == 3 || company_id == 14) { // STSK and SKP
                    $('#request_skp').removeAttr('hidden', true);
                    $('#type_skp').attr('required','required');

                    $('#request_all').attr('hidden','hidden');
                    $('#type_all').removeAttr('required', true);

                    $('#request_mmi').attr('hidden','hidden');
                    $('#type_mmi').removeAttr('required', true);
                }
                else {
                    $('#request_all').removeAttr('hidden', true);
                    $('#type_all').attr('required','required');

                    $('#request_mmi').attr('hidden','hidden');
                    $('#type_mmi').removeAttr('required', true);

                    $('#request_skp').attr('hidden','hidden');
                    $('#type_skp').removeAttr('required', true);
                }

            }  

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

            $('.note-popover').attr('hidden',true);
        });
    </script>

    <script>
        $(document).ready(function(){
            //define template
            var template = $('#sections .section:first').clone();

            //define counter
            var sectionsCount = 1;

            //add new section
            $('body').on('click', '.addsection', function() {

                //increment
                sectionsCount++;

                //loop through each input
                var section = template.clone().find(':input').each(function(){

                    //set id to store the updated section number
                    var newId = this.id + sectionsCount;

                    //update for label
                    $(this).prev().attr('for', newId);

                    //update id
                    this.id = newId;
                    this.value = '';
                    if ($(this).hasClass('qty')) {
                        this.value = 1;
                    }

                }).end()

                //inject new section
                .insertBefore('#add_more');
                return false;
            });

            //add value
            $('body').on('click', '.addValue', function() {
                $('.qty').val(1);
            });

            //remove section
            $('#sections').on('click', '.remove', function() {
                //fade out section
                if($('.remove').length>1){
                    $(this).parent().fadeOut(300, function(){
                        $(this).parent().empty();
                    });
                }
            });
        });
    </script>
@endpush
