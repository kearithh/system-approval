<div class="table-responsive">
    <table id="sections" class="table table-hover" style=" overflow-y: auto">
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
                            class="destination col"
                            type="text"
                            name="destination[]"
                            value={{ $value->destination }}
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
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date_start))->format('d-m-Y'))}}"
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
                            value="{{(\Carbon\Carbon::createFromTimestamp(strtotime($value->date_back))->format('d-m-Y'))}}"
                            style="height: 30px;"
                            required
                        >
                    </td>
                    <td>
                        <select required class="col-sm-12 unit" style="height: 30px" name="unit[]" id="">
                            <option value="1" @if($value->unit == 1) selected @endif >គិតជាម៉ាយ</option>
                            <option value="2" @if($value->unit == 2) selected @endif >គិតជាគីឡូ</option>
                        </select>
                    </td>
                    <td>
                        <input
                            class="start_number col"
                            min="0"
                            type="number"
                            id="start_number"
                            name="start_number[]"
                            value={{ $value->start_number }}
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="end_number col"
                            min="0"
                            type="number"
                            id="end_number"
                            name="end_number[]"
                            value={{ $value->end_number }}
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="miles_number col"
                            min="0"
                            type="number"
                            id="miles_number"
                            name="miles_number[]"
                            step="0.01"
                            value={{ $value->miles_number }}
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="km_number col"
                            min="0"
                            type="number"
                            id="km_number"
                            name="km_number[]"
                            step="0.01"
                            value={{ $value->km_number }}
                            required
                        >
                    </td>
                    <td>
                        <input
                            class="gasoline_number col"
                            min="0"
                            type="number"
                            id="gasoline_number"
                            name="gasoline_number[]"
                            step="0.01"
                            value={{ $value->gasoline_number }}
                            required
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
                <td colspan="9"></td>
            </tr>
            <tr style="background: #dee2e6">
                <td colspan="7" class="text-right">សរុប៖</td>
                <td>
                    <strong id="totalMiles"> {{ $data->total_miles }} </strong>
                    <input type="hidden" name="total_miles" id="total_miles" value="{{ $data->total_miles }}">
                </td>
                <td>
                    <strong id="totalKm"> {{ $data->total_km }} </strong>
                    <input type="hidden" name="total_km" id="total_km" value="{{ $data->total_km }}">
                </td>
                <td>
                    <strong id="totalGasoline"> {{ $data->total_gasoline }} </strong>
                    <input type="hidden" name="total_gasoline" id="total_gasoline" value="{{ $data->total_gasoline }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
