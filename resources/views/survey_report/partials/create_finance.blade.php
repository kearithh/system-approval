<div class="form-group">
  <b class="text-success">1. ការគ្រប់គ្រងសាច់ប្រាក់</b><br>
  <small>(បានរាប់សាច់ប្រាក់ និងផ្ទៀងផ្ទាត់ជាមួយ Cash Count Sheet, GL និង សាច់ប្រាក់ជាក់ស្ដែង)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage]" id="cash_manage_do" value="1" checked>
    <label class="form-check-label" for="cash_manage_do">
      បានរាប់
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage]" id="cash_manage_not_do" value="2">
    <label class="form-check-label" for="cash_manage_not_do">
      មិនបានរាប់
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage]" id="cash_manage_problem" value="3">
    <label class="form-check-label" for="cash_manage_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        placeholder="ករណីមិនបានរាប់ សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[cash_manage_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">2. ពិនិត្យសាច់ប្រាក់លើស/ខ្វះ(ត្រូវមានកំណត់ហេតុ)</b><br>
  <small>(បានត្រួតពិនិត្យ​ និងដោះស្រាយលើបញ្ហាសាច់ប្រាក់ លើស ឬខ្វះប្រចាំថ្ងៃ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage_doc]" id="cash_manage_doc_do" value="1" checked>
    <label class="form-check-label" for="cash_manage_doc_do">
      បានធ្វើ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage_doc]" id="cash_manage_doc_not_do" value="2">
    <label class="form-check-label" for="cash_manage_doc_not_do">
      មិនបានធ្វើ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_manage_doc]" id="cash_manage_doc_problem" value="3">
    <label class="form-check-label" for="cash_manage_doc_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea 
        placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ និងបញ្ជាក់ចំនួន" 
        class="form-control"
        name="finance[cash_manage_doc_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">3. ពិនិត្យ ការខ្ចប់សាច់ប្រាក់ប្រចាំថ្ងៃ</b><br>
  <small>(ពិនិត្យ ការខ្ចប់សាច់ប្រាក់ប្រចាំថ្ងៃ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[daily_cash]" id="daily_cash_have" value="1" checked>
    <label class="form-check-label" for="daily_cash_have">
      មាន
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[daily_cash]" id="daily_cash_not_have" value="2">
    <label class="form-check-label" for="daily_cash_not_have">
      មិនមាន
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[daily_cash]" id="daily_cash_problem" value="3">
    <label class="form-check-label" for="daily_cash_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[daily_cash_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">4. ពិនិត្យនិង អនុម័តលើឯកសារចំណាយ</b><br>
  <small>(ពិនិត្យនិង អនុម័តលើឯកសារចំណាយ និងចុះហត្ថលេខាលើឯកសារយោងបានត្រឹមត្រូវ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[approve_expense]" id="approve_expense_do" value="1" checked>
    <label class="form-check-label" for="approve_expense_do">
      បានពិនិត្យ និងអនុម័ត
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[approve_expense]" id="approve_expense_not_do" value="2">
    <label class="form-check-label" for="approve_expense_not_do">
      មិនបានពិនិត្យ និងអនុម័ត
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[approve_expense]" id="approve_expense_problem" value="3">
    <label class="form-check-label" for="approve_expense_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[approve_expense_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">5. ពិនិត្យការបិទបញ្ជី (MB win)</b><br>
  <small>(បានពិនិត្យលើការបិទបញ្ជីរបស់គណនេយ្យករបានទាន់ពេល)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_mb]" id="check_mb_do" value="1" checked>
    <label class="form-check-label" for="check_mb_do">
      បានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_mb]" id="check_mb_not_do" value="2">
    <label class="form-check-label" for="check_mb_not_do">
      មិនបានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_mb]" id="check_mb_problem" value="3">
    <label class="form-check-label" for="check_mb_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[check_mb_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">6. ពិនិត្យលើការ Post WO</b><br>
  <small>(ពិនិត្យលើការ Post WO ប្រមូលបាននៅក្នុង MBWin និង MBWin Tool បានត្រឹមត្រូវ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_wo]" id="check_wo_do" value="1" checked>
    <label class="form-check-label" for="check_wo_do">
      បានពិនិត្យត្រឹមត្រូវ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_wo]" id="check_wo_not_do" value="2">
    <label class="form-check-label" for="check_wo_not_do">
      មិនបានពិនិត្យត្រឹមត្រូវ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[check_wo]" id="check_wo_problem" value="3">
    <label class="form-check-label" for="check_wo_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[check_wo_node]"
    ></textarea>
  </div>
</div>

<div class="form-group">
  <b class="text-success">7. ប្រតិបត្តិការសាច់ប្រាក់នៅធនាគារ</b><br>
  <small>ត្រួតពិនិត្យលើការដាក់/ដកសាច់ប្រាក់នៅធនាគារ(ប្រាក់កម្ចី និង​ មេគង្គ)</small>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_bank]" id="cash_bank_do" value="1" checked>
    <label class="form-check-label" for="cash_bank_do">
      បានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_bank]" id="cash_bank_not_do" value="2">
    <label class="form-check-label" for="cash_bank_not_do">
      មិនបានពិនិត្យ
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="finance[cash_bank]" id="cash_bank_problem" value="3">
    <label class="form-check-label" for="cash_bank_problem">
      មានបញ្ហា
    </label>
  </div>
  <div>
    <textarea placeholder="ករណីមានបញ្ហា សូមបញ្ជាក់មូលហេតុ" 
        class="form-control"
        name="finance[cash_bank_node]"
    ></textarea>
  </div>
</div>
