<div class="form-group">
  <b class="text-success">1. ត្រួតពិនិត្យថ្ងៃចូលធ្វើការរបស់បុគ្គលិកថ្មី/ប្តូរតួនាទី/ប្តូរសាខា</b><br>
  <small>(បានជូនដំណឹងទៅធនធានមនុស្សចំពោះបុគ្គលិកប្តូរតួនាទី ប្តូរសាខា តាមសេចក្ដីសម្រេច ឬយឺតយ៉ាវ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[date_staff]" id="date_staff_do" value="1" 
           @if(@$data->hr->date_staff == 1) checked @endif>
    <label class="form-check-label" for="date_staff_do">
      បានជូនដំណឹង
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[date_staff]" id="date_staff_not_do" value="2"
            @if(@$data->hr->date_staff == 2) checked @endif>
    <label class="form-check-label" for="date_staff_not_do">
      មិនបានជូនដំណឹង
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[date_staff]" id="date_staff_problem" value="3"
            @if(@$data->hr->date_staff == 3) checked @endif>
    <label class="form-check-label" for="date_staff_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        class="form-control"
        name="hr[date_staff_node]"
        placeholder="ករណីមិនត្រឹមត្រូវតាមសេចក្ដីសម្រេច សូមបញ្ជាក់មូលហេតុ"
    >{{ @$data->hr->date_staff_node }}</textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">2. ពិនិត្យការខ្វះបុគ្គលិក</b><br>
  <small>(តាមដានបុគ្គលិកខ្វះក្នុងសាខា)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[staff_lack]" id="staff_lack_do" value="1" 
            @if(@$data->hr->staff_lack == 1) checked @endif>
    <label class="form-check-label" for="staff_lack_do">
      បានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[staff_lack]" id="staff_lack_not_do" value="2"
            @if(@$data->hr->staff_lack == 2) checked @endif>
    <label class="form-check-label" for="staff_lack_not_do">
      មិនបានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[staff_lack]" id="staff_lack_problem" value="3"
          @if(@$data->hr->staff_lack == 3) checked @endif>
    <label class="form-check-label" for="staff_lack_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        class="form-control"
        name="hr[staff_lack_node]"
        placeholder="ករណីខ្វះ(ខ្វះបុគ្គលិគចំនួនប៉ុន្មាននាក់? ប៉ុន្មានខែមកហើយ?)"
    >{{ @$data->hr->staff_lack_node }}</textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">3. ការចុះ Home Visit បុគ្គលិគថ្មី</b><br>
  <small>(បានចុះ Home Visit បុគ្គលិកថ្មី បានត្រឹមត្រូវ និងទាន់ពេល)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[home_visit]" id="home_visit_do" value="1" 
            @if(@$data->hr->home_visit == 1) checked @endif>
    <label class="form-check-label" for="home_visit_do">
      បានចុះ Home Visit
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[home_visit]" id="home_visit_not_do" value="2"
            @if(@$data->hr->home_visit == 2) checked @endif>
    <label class="form-check-label" for="home_visit_not_do">
      មិនបានចុះ Home Visit
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[home_visit]" id="home_visit_problem" value="3"
            @if(@$data->hr->home_visit == 3) checked @endif>
    <label class="form-check-label" for="home_visit_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        class="form-control"
        name="hr[home_visit_node]"
        placeholder="ករណីមានបញ្ហា បញ្ជាក់ពីមូលហេតុ"
    >{{ @$data->hr->home_visit_node }}</textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">4. ការចុះដោះស្រាយបុគ្គលិករំលោភកិច្ចសន្យា</b><br>
  <small>(បានចុះដោះស្រាយបុគ្គលិករំលោភកិច្ចសន្យា បានទាន់ពេលវេលា)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[violate_contract]" id="violate_contract_do" value="1" 
            @if(@$data->hr->violate_contract == 1) checked @endif>
    <label class="form-check-label" for="violate_contract_do">
      បានចុះទាន់ពេល
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[violate_contract]" id="violate_contract_not_do" value="2"
            @if(@$data->hr->violate_contract == 2) checked @endif>
    <label class="form-check-label" for="violate_contract_not_do">
      មិនបានចុះទាន់ពេល
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="hr[violate_contract]" id="violate_contract_problem" value="3"
            @if(@$data->hr->violate_contract == 3) checked @endif>
    <label class="form-check-label" for="violate_contract_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        class="form-control"
        name="hr[violate_contract_node]"
        placeholder="ករណីមានបញ្ហា បញ្ជាក់ពីមូលហេតុ"
    >{{ @$data->hr->violate_contract_node }}</textarea>
  </div>
</div>
