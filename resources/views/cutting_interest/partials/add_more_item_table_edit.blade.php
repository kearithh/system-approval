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
            @foreach($data->items as $key => $item)
                <tr class="section">
                    <td class="text-center">
                       <button type="button"
                               id="remove"
                               class="remove btn btn-sm btn-danger"
                               @if(@$item->interest_type > 0) disabled @endif
                       >
                           <i class="fa fa-trash"></i>
                       </button>
                       <input 
                            type="hidden"
                            name="interest_type[]"
                            required
                            value="{{@$item->interest_type}}" 
                        >
                    </td>
                    <td>
                        <input  
                            class="desc col-sm-12"
                            type="text"
                            id="desc"
                            name="desc[]"
                            value="{{$item->desc}}"
                            @if(@$item->interest_type > 0) readonly @endif
                            required
                        >
                    </td>
                    <td>
                        <select required class="col-sm-12 currency" style="height: 30px" name="currency[]" id="">
                            <option value="">----</option>
                            <option value="KHR" @if($item->currency=='KHR') selected @endif>រៀល</option>
                            <option value="USD" @if($item->currency=='USD') selected @endif>ដុល្លារ</option>
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="amount[]"
                            value="{{$item->amount}}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="amount_collect"
                            type="number"
                            id="amount_collect"
                            name="amount_collect[]"
                            value="{{$item->amount_collect}}"
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
                            value="{{$item->percentage}}"
                        >
                    </td>
                    <td>
                        <input
                            class="col"
                            type="text"
                            id="other"
                            name="other[]"
                            value="{{$item->other}}"
                        >
                    </td>
                </tr>
            @endforeach

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
