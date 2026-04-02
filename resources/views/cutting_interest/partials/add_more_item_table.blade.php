<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 300px">បរិយាយ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ទឹកប្រាក់ប្រមូលបាន</th>
            <th style="min-width: 120px">ភាគរយប្រមូលបាន(%)</th>
            <th style="min-width: 150px">ផ្សេងៗ</th>
        </thead>
        <tbody>
            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="1" 
                    >
                </td>
                <td>
                    <input 
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់ដើមជំពាក់នៅសល់"
                        readonly 
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input 
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="2" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ការប្រាក់ជំពាក់នៅសល់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="4" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ការប្រាក់ហួសកាលកំណត់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="9" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់ពិន័យពេលបង់ផ្តាច់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="3" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="សេវារដ្ឋបាលជំពាក់នៅសល់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="5" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់ពិន័យយឺតយ៉ាវ"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="6" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់ត្រូវបង់សរុប"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001"
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="7" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់ស្នើរសុំកាត់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001"
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
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
            </tr>

            <tr class="section">
                <td class="text-center">
                   <button type="button"
                           id="remove"
                           disabled 
                           class="remove btn btn-sm btn-danger"
                   >
                       <i class="fa fa-trash"></i>
                   </button>
                   <input 
                        type="hidden"
                        name="interest_type[]"
                        required
                        value="8" 
                    >
                </td>
                <td>
                    <input
                        class="desc col-sm-12"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="ប្រាក់អតិថិជនព្រមព្រៀងបង់"
                        readonly
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                        <option value="">----</option>
                        <option value="KHR" selected>រៀល</option>
                        <option value="USD">ដុល្លារ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="amount"
                        type="number"
                        id="amount"
                        name="amount[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="amount_collect"
                        type="number"
                        id="amount_collect"
                        name="amount_collect[]"
                    >
                </td>
                <td>
                    <input 
                        min="0" 
                        step="0.001" 
                        class="percentage"
                        type="number"
                        id="percentage"
                        name="percentage[]"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="text"
                        id="other"
                        name="other[]"
                        value="(១)"
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
        </tbody>
    </table>
</div>
