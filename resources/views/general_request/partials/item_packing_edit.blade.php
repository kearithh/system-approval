<div class="table-responsive">
    <table id="sections_packing" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 350px">ឈ្មោះ<span style='color: red'>*</span></th>
            <th style="min-width: 120px">ចំនួនប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 80px">រូបិយប័ណ្ណ<span style='color: red'>*</span></th>
            <th style="min-width: 300px">ចំនួនទឹកប្រាក់<span style='color: red'>*</span></th>
        </thead>
        <tbody>
            @foreach($data->items as $key => $item)
                <tr class="section_packing">
                    <td class="text-center">
                       <button type="button"
                               id="remove_packing"
                               class="remove_packing btn btn-sm btn-danger"
                       >
                           <i class="fa fa-trash"></i>
                       </button>
                    </td>
                    <td>
                        <input
                            value="{{ $item->name }}"
                            required
                            class="col-sm-12 name_packing"
                            type="text"
                            name="name_packing[]"
                        >
                    </td>
                    <td>
                        <input
                            value="{{ $item->qty }}"
                            required
                            class="qty_packing col"
                            min="1"
                            type="number"
                            name="qty_packing[]"
                        >
                    </td>
                    <td>
                        <select required class="col-sm-12 currency_packing" style="height: 30px" name="currency_packing[]">
                            <option value="">----</option>
                            <option value="KHR" @if($item->currency=='KHR') selected @endif>រៀល</option>
                            <option value="USD" @if($item->currency=='USD') selected @endif>ដុល្លារ</option>
                        </select>
                    </td>

                    <td>
                        <input
                            value="{{ $item->amount }}"
                            class="amount_packing col"
                            type="number"
                            name="amount_packing[]"
                            step="0.1"
                        >
                    </td>
                </tr>
            @endforeach

            <tr id="add_more_packing">
                <td class="text-center">
                    <button type="button"
                            id="addItem_packing"
                            class="addsection_packing btn btn-sm btn-success"
                    >
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
                <td colspan="4"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="3" class="text-right">សរុប៖</td>
                <td>
                    $ <strong id="total_packing">{{ $data->total_amount_usd }}</strong>
                    <input type="hidden" value="{{ $data->total_amount_usd }}" name="total_packing" id="total_input_packing">
                </td>
                <td>
                    <strong id="totalKHR_packing">{{ $data->total_amount_khr }}</strong><sup>៛</sup>
                    <input type="hidden" value="{{ $data->total_amount_khr }}" name="total_khr_packing" id="total_khr_input_packing">
                </td>
            </tr>
        </tbody>
    </table>
</div>
