<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="width: 70px;">សកម្ម</th>
            <th style="min-width: 200px">គោលដៅ<span style='color: red'>*</span></th>
            <th style="min-width: 100px">ថ្ងៃចេញដំណើរ<span style='color: red'>*</span></th>
            <th style="min-width: 100px">ថ្ងៃត្រឡប់មកវិញ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">ឯកត្តា<span style='color: red'>*</span></th>
            <th style="min-width: 200px">កុងទ័រចេញដំណើរ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">កុងទ័រត្រឡប់មកវិញ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">គិតជាម៉ាយ<span style='color: red'>*</span></th>
            <th style="min-width: 200px">គិតជាគីឡូ<span style='color: red'>*</span></th>
            <th style="min-width: 150px">
                ចំនួនសាំង/លីត
                <i 
                    class="fa fa-xs fa-question-circle tooltipsign" 
                    data-toggle="tooltip" title="ចំនួនសាំង(100KM = 11L)" 
                    data-placement="top">
                </i>
            </th>
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
                        class="destination col"
                        type="text"
                        name="destination[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="append-datepicker datepicker"
                        type="text"
                        name="date_start[]"
                        placeholder="dd-mm-yyyy"
                        autocomplete="off"
                        style="height: 30px;"
                        required
                    >
                </td>
                <td>
                    <input
                        class="append-datepicker datepicker"
                        type="text"
                        name="date_back[]"
                        placeholder="dd-mm-yyyy"
                        autocomplete="off"
                        style="height: 30px;"
                        required
                    >
                </td>
                <td>
                    <select required class="col-sm-12 unit" style="height: 30px" name="unit[]" id="">
                        <option value="1">គិតជាម៉ាយ</option>
                        <option value="2">គិតជាគីឡូ</option>
                    </select>
                </td>
                <td>
                    <input
                        class="start_number col"
                        min="0"
                        type="number"
                        id="start_number"
                        value="0"
                        name="start_number[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="end_number col"
                        min="0"
                        type="number"
                        id="end_number"
                        value="0"
                        name="end_number[]"
                        required
                    >
                </td>
                <td>
                    <input
                        class="miles_number col"
                        min="0"
                        type="number"
                        id="miles_number"
                        value="0"
                        name="miles_number[]"
                        step="0.01"
                        required
                    >
                </td>
                <td>
                    <input
                        class="km_number col"
                        min="0"
                        type="number"
                        id="km_number"
                        value="0"
                        name="km_number[]"
                        step="0.01"
                        required
                    >
                </td>
                <td>
                    <input
                        class="gasoline_number col"
                        min="0"
                        type="number"
                        id="gasoline_number"
                        value="0"
                        name="gasoline_number[]"
                        step="0.01"
                        required
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
                <td colspan="9"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="7" class="text-right">សរុប៖</td>
                <td>
                    <strong id="totalMiles"></strong>
                    <input type="hidden" name="total_miles" id="total_miles">
                </td>
                <td>
                    <strong id="totalKm"></strong>
                    <input type="hidden" name="total_km" id="total_km">
                </td>
                <td>
                    <strong id="totalGasoline"></strong>
                    <input type="hidden" name="total_gasoline" id="total_gasoline">
                </td>
            </tr>
        </tbody>
    </table>
</div>
