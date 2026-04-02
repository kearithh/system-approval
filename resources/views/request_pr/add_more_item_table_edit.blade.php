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
        @php
        //dd($data->items);
        @endphp
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
                    value="{{ $item->name }}"
                    required
                    class="col-sm-12"
                    type="text"
                    id="name"
                    name="name[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->desc }}"
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
                                                value="{{ $item->attachment }}"
                                            >

                                            &emsp;&emsp;
                                            @if(@$item->attachment)
                                                <a href="{{ asset('/'.@$item->attachment) }}" target="_self">View old File</a>
                                            @endif
                                            <input
                                                type="hidden"
                                                id="ss"
                                                class=""
                                                name="ss[]"
                                                value=""
                                            >
                                            <input type="hidden"
                                                    id="id_hidden"
                                                    class=""
                                                    name="id_hidden[]"
                                                    value="{{ $item->id }}">

            </td>
            <td>
                <input
                    value="{{ $item->qty }}"
                    required
                    class="qty col"
                    min="0.5"
                    step="0.5"
                    type="number"
                    id="qty"
                    name="qty[]"
                >
            </td>
            <td>
                <input
                    value="{{ $item->other }}"
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
                    value="{{ $item->ldp ? \Carbon\Carbon::parse($item->ldp)->format('Y-m-d') : '' }}"
                    placeholder="dd-mm-yyyy"
                    autocomplete="off" 
                >



            </td>
            <td>
                <input
                value="{{ $item->lunit_price }}"
                    required
                    class="col unit_price"
                    type="number"
                    id="lunit_price"
                    name="lunit_price[]"
                    step="0.0000001"
{{--                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'"--}}

                >
            </td>
            <td>
                <input
                value="{{ $item->lqty }}"
                    required
                    class="lqty col"
                    
                    type="number"
                    id="lqty"
                    name="lqty[]"
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
            
        </tr>
        </tbody>
    </table>
</div>
