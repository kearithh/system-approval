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

        $(".amount").inputmask();

        calculateAmount();

        function calculateAmount() {
            $('#sections').on('change keyup mouseover click', '.qty, .unit_price, .vat, .currency, .remove', function() {
                var qty = $(this).parent().parent().find('.qty').val();
                var unitPrice = $(this).parent().parent().find('.unit_price').val();

                // Amount
                var amount = qty*unitPrice;
                $(this).parent().parent().find('.amount').val(formatMoney(amount));
                $(this).parent().parent().find('.amount').data('value', parseFloat(amount));


                // Total
                var totalKHR = 0;
                var totalUSD = 0;
                $('#sections').find('.amount').each(function() {
                    var currency = $(this).parent().parent().find('.currency').val();
                    if(currency == 'KHR') {
                        totalKHR +=  parseFloat($(this).data('value'));
                    } else if(currency == 'USD') {
                        totalUSD +=  parseFloat($(this).data('value'));
                    } else {
                        // totalUSD +=  parseFloat($(this).data('value'));
                    }
                }).end();

                var total = 0;
                $('#sections').find('.qty').each(function() {
                    total +=  parseInt($(this).val());
                }).end();

                $('#total').text(total);
                $('#total_input').val(total);

                $('#totalUSD').text((formatMoney(totalUSD)));
                $('#total_usd_input').val((totalUSD));
                $('#totalKHR').text((formatMoney(totalKHR)));
                $('#total_khr_input').val((totalKHR));
            });
        }

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
