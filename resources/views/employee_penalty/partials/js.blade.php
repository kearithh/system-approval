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

            //formart currency
            function formatCurrency(total) {
                var neg = false;
                if(total < 0) {
                    neg = true;
                    total = Math.abs(total);
                }
                return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
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


            calculateAmount_customer();

            function calculateAmount_customer() {
                $('#sections_customer').on('change keyup mouseover click', 
                    '.indebted, .fraud, .system_rincipal, .system_rate, .system_total, .cut_rate, .cut_penalty, .remove_customer',
                    function() {
                    // Total
                    var total_indebted = 0;
                    var total_fraud = 0;
                    var total_system_rincipal = 0;
                    var total_system_rate = 0;
                    var total_system_total = 0;
                    var total_cut_rate = 0;
                    var total_cut_penalty = 0;

                    $('#sections_customer').find('.indebted').each(function() {
                        if($(this).val() > 0) {
                            total_indebted +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_indebted_text').text((formatCurrency(total_indebted)));

                    $('#sections_customer').find('.fraud').each(function() {
                        if($(this).val() > 0) {
                            total_fraud +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_fraud_text').text((formatCurrency(total_fraud)));

                    $('#sections_customer').find('.system_rincipal').each(function() {
                        if($(this).val() > 0) {
                            total_system_rincipal +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_system_rincipal_text').text((formatCurrency(total_system_rincipal)));

                    $('#sections_customer').find('.system_rate').each(function() {
                        if($(this).val() > 0) {
                            total_system_rate +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_system_rate_text').text((formatCurrency(total_system_rate)));

                    $('#sections_customer').find('.system_total').each(function() {
                        if($(this).val() > 0) {
                            total_system_total +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_system_total_text').text((formatCurrency(total_system_total)));

                    $('#sections_customer').find('.cut_rate').each(function() {
                        if($(this).val() > 0) {
                            total_cut_rate +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_cut_rate_text').text((formatCurrency(total_cut_rate)));

                    $('#sections_customer').find('.cut_penalty').each(function() {
                        if($(this).val() > 0) {
                            total_cut_penalty +=  parseFloat($(this).val());
                        }
                    }).end();
                    $('#total_cut_penalty_text').text((formatCurrency(total_cut_penalty)));

                });
            }


            //define template
            var template_customer = $('#sections_customer .section_customer:first').clone();

            //define counter
            var sectionsCount = 1;

            //add new section
            $('body').on('click', '.addsection_customer', function() {

                //increment
                sectionsCount++;

                //loop through each input
                var section = template_customer.clone().find(':input').each(function(){

                    //set id to store the updated section number
                    var newId = this.id + sectionsCount;

                    //update for label
                    $(this).prev().attr('for', newId);

                    //update id
                    this.id = newId;
                    this.value = '';

                }).end()

                //inject new section
                .insertBefore('#add_more_customer');
                return false;
            });


            //remove section
            $('#sections_customer').on('click', '.remove_customer', function() {
                //fade out section
                $(this).parent().fadeOut(300, function(){
                    $(this).parent().empty();
                });
            });
        });
        
    </script>
@endpush
