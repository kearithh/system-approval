@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        @if(session('status'))
            Swal.fire({
                title: 'Success',
                icon: 'success',
                timer: '2000',
            })
        @endif
    </script>

    <script>

        $(".select2").select2({
            // tags: true
        });
        $(".amount").inputmask();

        calculateAmount();

        function calculateAmount() {
            $('#sections').on('change keyup mouseover click', '.qty, .unit_price, .vat, .currency, .remove', function() {
                var qty = $(this).parent().parent().find('.qty').val();
                var vat = $(this).parent().parent().find('.vat').val();
                vat = vat ? vat : 0;
                var unitPrice = $(this).parent().parent().find('.unit_price').val();

                // Amount
                var amount = (qty*unitPrice) + (vat * (qty*unitPrice))/100;
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

                $('#total').text((formatMoney(totalUSD)));
                $('#total_input').val((totalUSD));
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

    </script>
@endpush
