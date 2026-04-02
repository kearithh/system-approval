@push('js')

    <script>

        $(document).ready(function(){

          var $reviewerMulti = $(".js-reviewer-multi").select2();
          var $shortMulti = $(".js-short-multi").select2();
          var $approver = $(".js-approver").select2();

          $(".check").on("click", function () {
              let categories = $(this).val();
              setDefault(categories);
          });

          $("#company_id").on("change", function () {
              let categories = 1;
              setDefault(categories);
          });

          $(".clear").on("click", function () {
              clearDefault();
          });

          function clearDefault(){
              $reviewerMulti.val(null).trigger("change");
              $shortMulti.val(null).trigger("change");
              $approver.val(null).trigger("change");
          }

          setDefault(1);
          function setDefault(category){

              clearDefault();

              $category = category;
              $company = $("#company_id").val();
              $department = $("#my_department").val();
              $type = $("#my_type").val();
              $type_request = $("#type_request").val();
              $type_report = $("#type_report").val();

              $.ajax({
                  type : 'get',
                  url : "{{URL::route('setting-reviewer-approver.find')}}",
                  data:{
                      'company': $company,
                      'department': $department,
                      'type': 'request',
                      'type_request': $type_request,
                      'type_report': $type_report,
                      'category': $category,
                  },

                  success:function(data){

                      console.log(data);

                      let reviewers = data.reviewers;
                      let reviewers_short = data.reviewers_short;
                      let approver = data.approver;

                      if(reviewers){
                          reviewers.forEach(function(element){
                              let eletment = $('.js-reviewer-multi option[value="'+element+'"]');
                              $reviewerMulti.append(eletment);
                          });
                          $reviewerMulti.val(reviewers).trigger("change");
                      }

                      if(reviewers_short){
                          reviewers_short.forEach(function(element){
                              let eletment = $('.js-short-multi option[value="'+element+'"]');
                              $shortMulti.append(eletment);
                          });
                          $shortMulti.val(reviewers_short).trigger("change");
                      }

                      if(approver){
                          $approver.val(approver).trigger("change");
                      }

                  }

              });
          }

      });

  </script>

@endpush