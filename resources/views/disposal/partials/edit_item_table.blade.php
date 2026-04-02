<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
        <th style="width: 70px;">សកម្ម</th>
        <th style="min-width: 200px">ឈ្មោះក្រុមហ៊ុន/សាខា</th>
        <th style="min-width: 300px">ឈ្មោះសំភារៈ<span style='color: red'>*</span></th>
        <th style="min-width: 150px">ប្រភេទសំភារៈ</th>
        <th style="min-width: 200px">កូដ</th>
        <th style="min-width: 150px">ម៉ាក</th>
        <th style="min-width: 120px">កាលបរិច្ឆេទទិញ</th>
        <th style="min-width: 120px">កាលបរិច្ឆេទខូច<span style='color: red'>*</span></th>
        <th style="min-width: 100px">បរិមាណ<span style='color: red'>*</span></th>
        <th style="min-width: 300px">មូលហេតុខូច<span style='color: red'>*</span></th>
        <th style="min-width: 250px">
            ឯកសារភ្ជាប់
               <i class="fa fa-xs fa-question-circle tooltipsign" data-toggle="tooltip"
                  title="ក្នុងករណី Asset មួយមានឯកសារភ្ជាប់ច្រើន សូមស្កេនបញ្ចូលគ្នាតែមួយ!"
                  data-placement="top"></i>
        </th>
        </thead>
        <tbody>

        @foreach($data->items as $item)
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
                        class="col-sm-12"
                        type="text"
                        id="company_name"
                        name="company_name[]"
                        value="{{ $item->company_name }}"
                    >
                </td>
                <td>
                    <input
                        class="col-sm-12"
                        type="text"
                        id="name"
                        name="name[]"
                        required
                        value="{{ $item->name }}"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        min="1"
                        type="text"
                        id="asset_type"
                        name="asset_tye[]"
                        value="{{ $item->asset_tye }}"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="text"
                        id="code"
                        name="code[]"
                        value="{{ $item->code }}"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="text"
                        id="model"
                        name="model[]"
                        value="{{ $item->model }}"
                    >
                </td>
                <td>
                    <input
                        class="col append-datepicker"
                        min="1"
                        type="text"
                        id="purchase_date"
                        name="purchase_date[]"
                        placeholder="dd-mm-yyyy"
                        autocomplete="off"
                        @if($item->purchase_date != null)
                            value="{{ $item->purchase_date->format('d-m-Y') }}"
                        @endif
                    >
                </td>
                <td>
                    <input
                        class="col append-datepicker"
                        type="text"
                        id="broken_date"
                        name="broken_date[]"
                        required
                        placeholder="dd-mm-yyyy"
                        value="{{ $item->broken_date->format('d-m-Y') }}"
                        autocomplete="off"
                    >
                </td>

                <td>
                    <input
                        class="col qty text-center"
                        type="number"
                        id="qty"
                        name="qty[]"
                        required
                        min="1"
                        value="{{ $item->qty }}"
                    >
                </td>

                <td>
                    <input 
                        required 
                        class="col"
                        type="text"
                        id="desc"
                        name="desc[]"
                        value="{{ $item->desc }}"
                    >
                </td>
                <td>
                    <input
                        class="col"
                        type="file"
                        id="attachment"
                        name="attachment[]"
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
        <tr style="display: none">
            <td colspan="6" class="text-right">សរុប</td>
            <td>
                $<strong id="total"></strong>
                <input type="hidden" name="total" id="total_input">
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
