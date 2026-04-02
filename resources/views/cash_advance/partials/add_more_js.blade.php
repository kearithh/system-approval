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

        checkCompany();

        $("#company_id").on('change', function () {
            checkCompany();
        });

        function checkCompany(){
            var com = $("#company_id").val();
            if (com == 6) { //mmi
                $("#note").val('បុរេប្រទាននេះ ត្រូវធ្វើការជម្រះនៅថ្ងៃបន្ទាប់ បន្ទាប់ពីបេសកកម្មត្រូវបានបញ្ចប់ និងមិនអោយលើសពី ២៤ម៉ោង នៃថ្ងៃធ្វើការ ។');
            }
            else {
                $("#note").val('បុរេប្រទាននេះ ត្រូវធ្វើការជម្រះរៀងរាល់ថ្ងៃច័ន្ទ បន្ទាប់ពីបញ្ចប់បេសកម្ម ឬទទួលបានឯកសារគ្រប់គ្រាន់ ។');
            }
        }

        $('#advance, #expense').on('change keyup', function() {
            checkExpense();
        });

        function checkExpense(){
            var advance = parseInt($('#advance').val());
            var expense = parseInt($('#expense').val());
            $('#company').val(advance - expense);
            $('#staff').val(expense - advance);
        }

        $('#currency_advance').on('change keyup', function() {
            var expenseKHR = parseInt($('#total_khr_input').val());
            var expenseUSD = parseInt($('#total_input').val());
            calculateExpense(expenseKHR, expenseUSD);
        });

        function calculateExpense(expenseKHR, expenseUSD){
            let currency_advance = $('#currency_advance').val();
            if (currency_advance == 'KHR') {
                $('#expense').val((expenseKHR));
            } else {
                $('#expense').val((expenseUSD));
            }
            checkExpense();
        }
        
        $(".amount").inputmask();

        checkType();

        $("#type_advance").on('change load', function(event) {
            checkType();
        });

        function checkType(){
            var type = $('#type_advance').val();
            if (type == {{ config('app.advance') }}) { // advane
                $('.form_clear').attr('hidden','hidden');
                $('.clear_advacne').removeAttr('required');
                $('.not_reimbursement').val('');
            }
            else if (type == {{ config('app.clear_advance') }}){ // clear cash advance
                $('.form_clear').removeAttr('hidden', true);
                $('.clear_advacne').attr('required','required');
            }
            else if (type == {{ config('app.reimbursement') }}){ // reimbursement
                $('.form_clear').removeAttr('hidden', true);
                $('.clear_advacne').attr('required','required');
                $('.form_not_reimbursement').attr('hidden','hidden');
                $('.not_reimbursement').removeAttr('required');
                $('.not_reimbursement').val('');
            }
        }

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

                calculateExpense(totalKHR, totalUSD);

                //checkExpense();
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
