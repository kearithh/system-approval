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

            // $(".amount").inputmask();

            calculateAmount();

            function calculateAmount() {
                $('#sections').on('change keyup mouseover click', '.amount, .remove', function() {
                    // Total
                    var totalKHR = 0;
                    var totalUSD = 0;
                    $('#sections').find('.amount').each(function() {
                        totalKHR +=  parseFloat($(this).val());
                    }).end();
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
                    if ($(this).hasClass('vat')) {
                        this.value = 0;
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


            //define template
            var template_staff = $('#sections_staff .section_staff:first').clone();

            //define counter
            var sectionsCountStaff = 1;

            //add new section
            $('body').on('click', '.addsectionsStaff', function() {

                //increment
                sectionsCountStaff++;

                var section_staff = template_staff.clone().find(':input').each(function(){

                    //set id to store the updated section number
                    var newIdStaff = this.id + sectionsCountStaff;

                    //update for label
                    $(this).prev().attr('for', newIdStaff);

                    //update id
                    this.id = newIdStaff;

                }).end()


                //inject new section
                .insertBefore('#add_more_staff');
                return false;
            });

            //remove section
            $('#sections_staff').on('click', '.remove_staff', function() {
                //fade out section
                if($('.remove_staff').length > 1){
                    $(this).parent().fadeOut(300, function(){
                        $(this).parent().empty();
                    });
                }
            });
        });

    </script>
@endpush
