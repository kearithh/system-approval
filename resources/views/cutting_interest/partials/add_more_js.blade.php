@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
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
    </script>

    <script>

        $(document).ready(function(){

            // check file upload
            $("#penalty").submit( function(submitEvent) {
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

            CalculateInterestRate();

            function CalculateInterestRate() {
                $('#sections_cutting').on('change keyup mouseover click', '#remain_amount, #interest_repay, #period', function() {
                    // parseFloat(costs.val().replace(",",".")) || 0;
                    let remain_amount = parseFloat($('#remain_amount').val() || 0) ;
                    let interest_repay = parseFloat($('#interest_repay').val() || 0);
                    let period = parseFloat($('#period').val() || 0);
                    let interest_rate = 0;
                    // i% = I/(P*(n+1)/2
                    interest_rate = (interest_repay / (remain_amount * (period + 1) / 2)) * 100;
                    //interest_rate = (100000 / (2000000 * (6 + 1) / 2)) * 100;
                    $('#interest_rate').val(interest_rate.toFixed(2));
                });
            }

            $(".select2").select2({
                // tags: true
            });

            $("select").on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });

            $('.desc_textarea').summernote({
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
                ]
            });

          $('.note-popover').attr('hidden',true);

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

                    if ($(this).hasClass('remove')) {
                        $(this).removeAttr('disabled');
                    }

                    if ($(this).hasClass('desc')) {
                        $(this).removeAttr('readonly');
                    }

                }).end()

                //inject new section
                .insertBefore('#add_more');
                return false;
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
