@push('js')
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

        $("#requestForm").submit( function(submitEvent) {
            $(".error").remove();
            $(".validate_time").css("border-color", "#ced4da");
            var hour = parseInt($("#total").val()) || 0;
            var minute = parseInt($("#total_minute").val()) || 0;
            var calculate = (hour * 60) + minute ;
            if (calculate < 30) {
                $(".validate_time").css("border-color", "red");
                $("#validate").after('<span class="error" style="color:red">Duration can not be less than 30 minutes</span>');
                submitEvent.preventDefault();
            }

            // // check incurrect staff id
            // var is_staff = $("#is-staff-code").val();
            // if (is_staff != 1) {
            //     $("#staff_code").css("border-color", "red");
            //     $("#staff_code").focus();
            //     $("#message-id").css("color", "red");
            //     $("#message-id").html('<span>Message: សូមចុច Check ID ដើម្បីបញ្ជាក់ថា Saff ID ពិតជាត្រឹមត្រូវ!!!</span>');
            //     submitEvent.preventDefault();
            // }
        });

        $('#staff').on('change', function () {
            $system_user_id = $('option:selected', this).data('system_user_id');
            $("#staff_code").val($system_user_id);

            $position = $('option:selected', this).data('position');
            $("#position").val($position).trigger('change');
            check_staff();
        });

        $("#check-id").on("click", function( event ) {
            check_staff();
        });

        function check_staff() {
            $("#is-staff-code").val("");
            $("#message-id").text("");

            $("#staff_code").css("border-color", "#ced4da");

            $staff_code = $("#staff_code").val();
            $url = `{{ route("request_ot.check-staff") }}`;

            $.ajax({
                url     : $url,
                data    : {'staff_code': $staff_code},
                type    : 'GET',
                success: function( response ) {
                    if (response.status == 1) {
                        $("#message-id").css("color", "green");
                        $("#is-staff-code").val(1);
                        $("#message-id").html('<span> Message: Successfully <br> Staff Name: ' + response.data.full_name + '</span>');
                    }
                    else {
                        $("#staff_code").css("border-color", "red");
                        $("#message-id").css("color", "red");
                        $("#message-id").html('<span>Message: Incorrect your id <br> Please check your id with HR </span>');
                    }
                },
                error: function( err, msg ) {
                    console.log(err);
                },
                async: true
            });
        }

        $("#back").on("click", function( event ) {
            if(localStorage.previous){
                window.location.href = localStorage.previous;
                window.localStorage.removeItem('previous');
            }
            else{
                alert("Can't previous");
            }
        });

        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight:true,
            autoclose: true
        });

        $(".select2").select2({
            tags: true,
            placeholder: {
                id: null, // the value of the option
                text: ' << ជ្រើសរើស >> '
            }
        });

        $(".my_select").select2({
            //tags: true
            placeholder: {
                id: null, // the value of the option
                text: ' << ជ្រើសរើស >> '
            }
        });

        $("select").on("select2:select", function (evt) {
            var element = evt.params.data.element;
            var $element = $(element);
            
            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });
        
      });
    </script>
@endpush
