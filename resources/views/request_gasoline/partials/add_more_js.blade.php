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

        $('#price_per_l').on('change keyup mouseover click', function() {
            checkExpense();
        });

        function checkExpense(){
            let price_per_l = parseFloat($('#price_per_l').val());
            let total_gasoline_cal = parseFloat($('#total_gasoline').val());
            let total_expense = parseInt(total_gasoline_cal * price_per_l);
            $('#total_expense').val((total_expense / 100).toFixed() * 100); // riel currency 1569 = 1600

        }
        
        $(".amount").inputmask();

        calculateAmount();

        function calculateAmount() {
            $('#sections').on('change keyup mouseover click', '.unit, .start_number, .end_number, .miles_number, .km_number, .gasoline_number, .remove', function() {

                var km_l = 11; // set defalt = 100KM = 11L
                var unit = $(this).parent().parent().find('.unit').val();
                var start_number = $(this).parent().parent().find('.start_number').val();
                var end_number = $(this).parent().parent().find('.end_number').val();

                // Amount
                var amount = parseInt(end_number) - parseInt(start_number);
                let miles_number = km_number = gasoline_number = 0;

                if(unit == 1) {
                    miles_number = parseFloat(amount);
                    km_number = parseFloat(amount * 1.609);
                } else {
                    miles_number = parseFloat(amount / 1.609);
                    km_number = parseFloat(amount);
                }

                gasoline_number = (km_number / 100) * km_l;

                $(this).parent().parent().find('.miles_number').val(miles_number.toFixed(2));
                $(this).parent().parent().find('.miles_number').data('value', miles_number);
                $(this).parent().parent().find('.km_number').val(km_number.toFixed(2));
                $(this).parent().parent().find('.km_number').data('value', km_number);
                $(this).parent().parent().find('.gasoline_number').val(gasoline_number.toFixed(2));
                $(this).parent().parent().find('.gasoline_number').data('value', gasoline_number);

                // Total
                var totalMiles = totalKm = totalGasoline = 0;
                $('#sections').find('.unit').each(function() {
                    totalMiles += parseFloat($(this).parent().parent().find('.miles_number').val());
                    totalKm += parseFloat($(this).parent().parent().find('.km_number').val());
                    totalGasoline += parseFloat($(this).parent().parent().find('.gasoline_number').val());
                }).end();

                $('#totalMiles').text(totalMiles);
                $('#total_miles').val(totalMiles);
                $('#totalKm').text(totalKm);
                $('#total_km').val(totalKm);
                $('#totalGasoline').text(totalGasoline);
                $('#total_gasoline').val(totalGasoline);
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
