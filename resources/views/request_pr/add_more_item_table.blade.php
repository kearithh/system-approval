<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
        <th style="width: 70px;">ល.រ/No</th>
        <th style="min-width: 350px">មុខទំនិញ/Item<span style='color: red'>*</span></th>
        <th style="min-width: 300px">ទំហំ Scope/Specification</th>
        <th style="min-width: 300px">រូបភាព/Pictures</th>
        <th style="min-width: 120px">បរិមាណ/Quantity<span style='color: red'>*</span></th>
        {{-- <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ថ្លៃឯកត្តា<span style='color: red'>*</span></th>
        <th style="min-width: 120px">ពន្ធ(%)<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ទឹកប្រាក់</th> --}}
        <th style="min-width: 150px">ឯកតា/Unit</th>
        <th style="min-width: 150px">ថ្ងៃទិញចុងក្រោយ<br>Last Date of Purchasing<span style='color: red'></span></th>
        <th style="min-width: 150px">ថ្លៃឯកតាទិញចុងក្រោយ<br>Last Unit purchase price<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ចំនួននៅសល់<br>Remain QTY<span style='color: red'>*</span></th>
        </thead>
        <tbody>

        <tr class="section">
            <td class="text-center">
               <button type="button"
                       id="remove"
                       class="remove btn btn-sm btn-danger"
               >
                   <i class="fa fa-trash"></i>
               </button>
            </td>
            <td>
                <input
                    required
                    class="col-sm-12"
                    type="text"
                    id="name"
                    name="name[]"
                >
            </td>
            <td>
                <input
                    class="col-sm-12"
                    type="text"
                    id="desc"
                    name="desc[]"
                >
            </td>
            <td>
                            <input
                                type="file"
                                id="file_name"
                                class="{{ $errors->has('file_name') ? ' is-invalid' : '' }}"
                                name="file_name[]"
                                value="{{ old('file_name') }}"
                            >
                        
            </td>
            <td>
                <input
                    required
                    class="qty col"
                    value="1"
                    min="0.5"
                    step="0.5"
                    type="number"
                    id="qty"
                    name="qty[]"
                >
            </td>
            
            <td>
                <input
                    class="col"
                    type="text"
                    id="other"
                    name="other[]"
                >
            </td>
            <td>
                <input 
                              
                              type="date"
                              id="ldp"
                              name="ldp[]"
                              data-inputmask-inputformat="dd-mm-yyyy"
                              placeholder="dd-mm-yyyy"
                              autocomplete="off" 
                        >
            </td>
            <td>
                <input
                    required
                    class="col unit_price"
                    value="0"
                    type="number"
                    id="lunit_price"
                    name="lunit_price[]"
                    step="0.0000001"
{{--                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"--}}

                >
            </td>
            <td>
                <input
                    required
                    class="lqty col"
                    value="0"
                    
                    type="number"
                    id="lqty"
                    name="lqty[]"
                >
            </td>
        </tr>


        <tr id="add_more">
            <td class="text-center">
                <button type="button"
                        id="addItem"
                        class="addsection btn btn-sm btn-success"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </td>
            <td colspan="6"></td>
        </tr>
        <tr style="background: #dee2e6">
            
        </tr>
        </tbody>
    </table>
</div>


