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
            // tags: true
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

        $(document).on('click', '.append-datepicker', function(){
            $(this).datepicker({
                format: 'dd-mm-yyyy',
                todayHighlight:true
            }).focus();
        });

        $('#advance').on('change keyup mouseover click', function() {
            checkExpense();
        });

        function checkExpense(){
            var advance = parseInt($('#advance').val());
            var expense = parseInt($('#expense').val());
            $('#company').val(advance - expense);
            $('#staff').val(expense - advance);
        }
        
        $(".amount").inputmask();

        calculateAmount();

        function calculateAmount() {
            $('#sections').on('change keyup mouseover click', '.diet, .fees, .remove', function() {

                var diet = $(this).parent().parent().find('.diet').val();
                var fees = $(this).parent().parent().find('.fees').val();

                // Amount
                var amount = parseFloat(diet) + parseFloat(fees);
                $(this).parent().parent().find('.amount').val(formatMoney(amount));
                $(this).parent().parent().find('.amount').data('value', parseFloat(amount));


                // Total
                var totalDiet = totalFees = totalAmount = 0;
                $('#sections').find('.amount').each(function() {
                    totalDiet += parseFloat($(this).parent().parent().find('.diet').val());
                    totalFees += parseFloat($(this).parent().parent().find('.fees').val());
                    // totalAmount +=  parseFloat($(this).data('value'));
                }).end();
                totalAmount = totalDiet + totalFees;
                $('#totalDiet').text((formatMoney(totalDiet)));
                $('#total_diet').val((totalDiet));
                $('#totalFees').text((formatMoney(totalFees)));
                $('#total_fees').val((totalFees));
                $('#totalAmount').text((formatMoney(totalAmount)));
                $('#total_amount').val((totalAmount));
                $('#expense').val((totalAmount));
                checkExpense();
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
