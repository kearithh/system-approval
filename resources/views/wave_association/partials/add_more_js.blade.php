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

            calculateAmount();

            function calculateAmount() {
                $('#sections').on('change keyup mouseover click', ' .amount, .currency, .remove', function() {

                    // Total
                    var totalKHR = 0;
                    var totalUSD = 0;
                    $('#sections').find('.amount').each(function() {
                        var currency = $(this).parent().parent().find('.currency').val();
                        if(currency == 'KHR') {
                            totalKHR +=  parseFloat($(this).val());
                        } else if(currency == 'USD') {
                            totalUSD +=  parseFloat($(this).val());
                        } else {
                            // totalUSD +=  parseFloat($(this).data('value'));
                        }
                    }).end();

                    $('#total').text((formatMoney(totalUSD)));
                    $('#total_input').val((totalUSD));
                    $('#totalKHR').text((formatMoney(totalKHR)));
                    $('#total_khr_input').val((totalKHR));
                    console.log(totalKHR);
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
