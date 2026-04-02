@push('js')
    <script>
        $(".select2").select2({
            // tags: true
        });
        $(".amount").inputmask();

        calculateAmount();
        function calculateAmount() {
            $('#sections').on('keyup', '.unit_price, .qty', function() {
                var qty = $(this).parent().parent().find('.qty').val();
                var unitPrice = $(this).parent().parent().find('.unit_price').val();
                console.log("qty "+qty+" , unit price "+ unitPrice);

                $(this).parent().parent().find('.amount').val(formatMoney(parseFloat(qty)*parseFloat(unitPrice)));
                $(this).parent().parent().find('.amount').data('value', (parseFloat(qty)*parseFloat(unitPrice)));

                var totalPrice = 0;
                $('#sections').find('.amount').each(function()
                {
                    totalPrice +=  parseFloat($(this).data('value'));
                }).end();

                $('#total').text((formatMoney(totalPrice)));
                $('#total_input').val((totalPrice));
                return false;
            });
        }
    </script>
    <script src="{{ asset('add_more_section.js') }}"></script>
@endpush
