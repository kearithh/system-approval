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

            $( "#back" ).on( "click", function( event ) {
                if(localStorage.previous){
                    window.location.href = localStorage.previous;
                    window.localStorage.removeItem('previous');
                }
                else{
                    alert("Can't previous");
                }
            });

            //order reviewer by select
            $("select").on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);

                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });

            $(document).on('click', '.append-datepicker', function(){
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight:true,
                    autoclose: true
                }).focus();
            });

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
