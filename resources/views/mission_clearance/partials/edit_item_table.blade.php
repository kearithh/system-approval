<div class="table-responsive">
    <table id="sections" class="table table-hover" style=" overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 100px">ថ្ងែចំណាយ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">បេសកកម្មសាខា<span style='color: red'>*</span></th>
            <th style="min-width: 200px">របបអាហារ(៛)<span style='color: red'>*</span></th>
            <th style="min-width: 200px">ចំណាយផ្សេងៗ(៛)<span style='color: red'>*</span></th>
            <th style="min-width: 200px">សរុបទឹកប្រាក់(៛)</th>
            <th style="min-width: 150px">បរិយាយចំណាយ</th>
        </thead>
        <tbody>

            @foreach($data->items as $key => $value)
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
                            class="append-datepicker datepicker"
                            type="text"
                            name="date[]"
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date))->format('d-m-Y'))}}"
                            placeholder="dd-mm-yyyy"
                            autocomplete="off"
                            style="height: 30px;"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="branch_name"
                            name="branch_name[]"
                            value="{{ $value->branch_name }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="diet col"
                            min="0"
                            type="number"
                            id="diet"
                            name="diet[]"
                            value="{{ $value->diet }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="fees col"
                            min="0"
                            type="number"
                            id="fees"
                            name="fees[]"
                            value="{{ $value->fees }}"
                            required
                        >
                    </td>
                    <td>
                        <input
                            readonly="true"
                            class="amount col-sm-12"
                            type="text"
                            id="amount"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"
                            name="amount[]"
                            value="{{ $value->amount }}"
                        >
                    </td>
                    <td>
                        <textarea name="desc[]" rows="1">{{ $value->remark }}</textarea>
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
                <td colspan="3" class="text-right">សរុប៖</td>
                <td>
                    <strong id="totalDiet">{{ $data->total_diet }}</strong>៛
                    <input type="hidden" name="total_diet" id="total_diet" value="{{ $data->total_diet }}">
                </td>
                <td>
                    <strong id="totalFees">{{ $data->total_fees }}</strong>៛
                    <input type="hidden" name="total_fees" id="total_fees" value="{{ $data->total_fees }}">
                </td>
                <td>
                    <strong id="totalAmount">{{ $data->total }}</strong>៛
                    <input type="hidden" name="total_amount" id="total_amount" value="{{ $data->total }}">
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
