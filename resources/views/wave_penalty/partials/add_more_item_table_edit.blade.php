<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 250px">ឈ្មោះ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">បរិយាយ</th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់ស្នើរសុំកាត់ពិន័យ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ទឹកប្រាក់ពិន័យទទួលបាន</th>
            <th style="min-width: 250px">មូលហេតុ</th>
        </thead>
        <tbody>
            @foreach($data->items as $key => $item)
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
                            value="{{$item->name}}"
                        >
                    </td>
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="desc"
                            name="desc[]"
                            value="{{$item->desc}}"
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
                            required 
                            step="0.0001"
                            class="amount"
                            type="number"
                            id="amount"
                            name="amount[]"
                            value="{{$item->amount}}"
                        >
                    </td>
                    <td>
                        <input
                            class="amount_collect"
                            type="number"
                            step="0.0001"
                            id="amount_collect"
                            name="amount_collect[]"
                            value="{{$item->amount_collect}}"
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
            <tr style="background: #dee2e6">
                <td colspan="5" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total">{{ $data->total_amount_usd }}</strong>
                    <input type="hidden" value="{{ $data->total_amount_usd }}" name="total" id="total_input">
                </td>
                <td>
                    <strong id="totalKHR">{{ $data->total_amount_khr }}</strong><sup>៛</sup>
                    <input type="hidden" value="{{ $data->total_amount_khr }}" name="total_khr" id="total_khr_input">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
