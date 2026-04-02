
<div class="form-group">
    <b class="text-success">1. ចំនួនអតិថិជនបានផ្សព្វផ្សាយក្នុងខែ</b><br>
    <div>
        <input type="number" name="number_cutomer" value="{{ @$data->number_customer }}" class="form-control" required>
    </div>
</div>

<div>
    <b class="text-success">2. ប្រៀបធៀបលទ្ធផលប្រចាំខែ</b><br>
    <small>ប្រៀបធៀបលទ្ធផលប្រចាំខែ (លទ្ធផលបច្ចុប្បន្ន ធៀបជាមួយ លទ្ធផលខែចាស់)</small>
</div>

<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="min-width: 210px">បរិយាយលទ្ធិផល<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនូន<span style='color: red'>*</span></th>
            <th style="min-width: 170px">លំអៀងធៀបខែចាស់<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនួនកើន ឬថយ<span style='color: red'>*</span></th>
            <th style="min-width: 280px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</th>
        </thead>
        <tbody>
            @if(@$data->compare_monthly)
                <?php 
                    $compare_monthly = is_array(@$data->compare_monthly) ? @$data->compare_monthly : json_decode(@$data->compare_monthly); 
                ?>
                @foreach(@$compare_monthly as $monthly )
                    <tr class="section">
                        <td>
                            <input 
                                class="col-sm-12"
                                type="text"
                                id="name"
                                name="compare_monthly_name[]"
                                value="{{ @$monthly->name }}"
                                readonly 
                            >
                        </td>
                        <td>
                            <input 
                                class="total"
                                type="number"
                                name="compare_monthly_total[]"
                                value="{{ @$monthly->total }}" 
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                                <option value="">----</option>
                                @foreach(config('app.bias_type') as $key => $value)
                                    <option value="{{ $value->val }}" @if($value->val == @$monthly->bias) selected @endif
                                    >
                                        {{ $value->name_km }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input 
                                class="amount"
                                type="number"
                                id="{{ @$monthly->amount }}"
                                name="compare_monthly_amount[]"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <textarea 
                                autocomplete="off" 
                                class="reason"
                                rows="1" 
                                name="compare_monthly_reason[]"
                                style="min-width: 280px"
                            >{{ @$monthly->reason }}</textarea>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="section">
                    <td>
                        <input 
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_monthly_name[]"
                            value="ចំនួនអតិថិជនក្នុងខែ"
                            readonly 
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_monthly_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input 
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_monthly_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_monthly_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_monthly_name[]"
                            value="ទឹកប្រាក់សកម្មក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_monthly_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_monthly_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_monthly_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_monthly_name[]"
                            value="ទឹកប្រាក់យឺតយ៉ាវក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_monthly_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_monthly_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_monthly_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_monthly_name[]"
                            value="ទឹកប្រាក់ប្រមូល (WO) ក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_monthly_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_monthly_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_monthly_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_monthly_name[]"
                            value="ទឹកប្រាក់មេគង្គលក់​បានក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_monthly_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_monthly_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_monthly_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_monthly_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>
            @endif

        </tbody>
    </table>
</div>


<div>
    <b class="text-success">3. ប្រៀបធៀបលទ្ធផលប្រចាំថ្ងៃ</b><br>
    <small>ប្រៀបធៀបលទ្ធផលប្រចាំថ្ងៃ (លទ្ធផលបច្ចុប្បន្ន ធៀបជាមួយ លទ្ធផលថ្ងៃចាស់)</small>
</div>

<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="min-width: 210px">បរិយាយលទ្ធិផល<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនូន<span style='color: red'>*</span></th>
            <th style="min-width: 170px">លំអៀងធៀបថ្ងែចាស់<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនួនកើន ឬថយ<span style='color: red'>*</span></th>
            <th style="min-width: 280px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</th>
        </thead>
        <tbody>
            @if(@$data->compare_daily)
                <?php 
                    $compare_daily = is_array(@$data->compare_daily) ? @$data->compare_daily : json_decode(@$data->compare_daily); 
                ?>
                @foreach(@$compare_daily as $daily )
                    <tr class="section">
                        <td>
                            <input 
                                class="col-sm-12"
                                type="text"
                                id="name"
                                name="compare_daily_name[]"
                                value="{{ @$daily->name }}"
                                readonly 
                            >
                        </td>
                        <td>
                            <input 
                                class="total"
                                type="number"
                                name="compare_daily_total[]"
                                value="{{ @$daily->total }}"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                                <option value="">----</option>
                                @foreach(config('app.bias_type') as $key => $value)
                                    <option value="{{ $value->val }}" @if($value->val == @$daily->bias) selected @endif
                                    >
                                        {{ $value->name_km }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input 
                                class="amount"
                                type="number"
                                id="amount"
                                name="compare_daily_amount[]"
                                value="{{ @$daily->amount }}"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <textarea 
                                autocomplete="off" 
                                class="reason"
                                rows="1" 
                                name="compare_daily_reason[]"
                                style="min-width: 280px"
                            >{{ @$daily->reason }}</textarea>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="section">
                    <td>
                        <input 
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_daily_name[]"
                            value="ចំនួនអតិថិជនក្នុងថ្ងៃ"
                            readonly 
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_daily_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input 
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_daily_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_daily_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_daily_name[]"
                            value="ទឹកប្រាក់សកម្មក្នុងថ្ងៃ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_daily_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_daily_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_daily_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_daily_name[]"
                            value="ទឹកប្រាក់យឺតយ៉ាវក្នុងថ្ងៃ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_daily_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_daily_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_daily_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_daily_name[]"
                            value="ទឹកប្រាក់ប្រមូល (WO) ក្នុងថ្ងៃ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_daily_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_daily_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_daily_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_daily_name[]"
                            value="ទឹកប្រាក់មេគង្គលក់​បានក្នុងថ្ងៃ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_daily_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_daily_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_daily_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_daily_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>
            @endif

        </tbody>
    </table>
</div>


<div>
    <b class="text-success">4. ប្រៀបធៀបលទ្ធផលជាមួយផែនការ</b><br>
    <small>សូមធ្វើការប្រៀបធៀបលទ្ធផលការងារចាក់ស្ដែងក្នុងសាខា ​និងក្នុងខែ ធៀបនិងផែនការ។</small>
</div>

<div class="table-responsive">
    <table id="sections" class="table table-hover" style="display: block; overflow-y: auto">
        <thead class="card-header ">
            <th style="min-width: 210px">បរិយាយលទ្ធិផល<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនូន<span style='color: red'>*</span></th>
            <th style="min-width: 170px">លំអៀងធៀបផែនការ<span style='color: red'>*</span></th>
            <th style="min-width: 170px">ចំនួនកើន ឬថយ<span style='color: red'>*</span></th>
            <th style="min-width: 280px">ករណីមិនសម្រចបញ្ជាក់មូលហេតុ</th>
        </thead>
        <tbody>
            @if(@$data->compare_plan)
                <?php 
                    $compare_plan = is_array(@$data->compare_plan) ? @$data->compare_plan : json_decode(@$data->compare_plan); 
                ?>
                @foreach(@$compare_plan as $plan )
                    <tr class="section">
                        <td>
                            <input 
                                class="col-sm-12"
                                type="text"
                                id="name"
                                name="compare_plan_name[]"
                                value="{{ @$plan->name }}"
                                readonly 
                            >
                        </td>
                        <td>
                            <input 
                                class="total"
                                type="number"
                                name="compare_plan_total[]"
                                value="{{ @$plan->total }}"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                                <option value="">----</option>
                                @foreach(config('app.bias_type') as $key => $value)
                                    <option value="{{ $value->val }}" @if($value->val == @$plan->bias) selected @endif
                                    >
                                        {{ $value->name_km }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input 
                                class="amount"
                                type="number"
                                id="amount"
                                name="compare_plan_amount[]"
                                value="{{ @$plan->amount }}"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <textarea 
                                autocomplete="off" 
                                class="reason"
                                rows="1" 
                                name="compare_plan_reason[]"
                                style="min-width: 280px"
                            >{{ @$plan->reason }}</textarea>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="section">
                    <td>
                        <input 
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_plan_name[]"
                            value="ចំនួនអតិថិជនក្នុងខែ"
                            readonly 
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_plan_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input 
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_plan_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_plan_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_plan_name[]"
                            value="ទឹកប្រាក់សកម្មក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_plan_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_plan_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_plan_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_plan_name[]"
                            value="ទឹកប្រាក់យឺតយ៉ាវក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_plan_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_plan_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_plan_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_plan_name[]"
                            value="ទឹកប្រាក់ប្រមូល (WO) ក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_plan_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_plan_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_plan_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>

                <tr class="section">
                    <td>
                        <input
                            class="col-sm-12"
                            type="text"
                            id="name"
                            name="compare_plan_name[]"
                            value="ទឹកប្រាក់មេគង្គលក់​បានក្នុងខែ"
                            readonly
                        >
                    </td>
                    <td>
                        <input 
                            class="total"
                            type="number"
                            name="compare_plan_total[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <select  class="col-sm-12 bias" style="height: 30px" name="compare_plan_bias[]" id="">
                            <option value="">----</option>
                            @foreach(config('app.bias_type') as $key => $value)
                                <option value="{{ $value->val }}">
                                    {{ $value->name_km }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input
                            class="amount"
                            type="number"
                            id="amount"
                            name="compare_plan_amount[]"
                            autocomplete="off"
                        >
                    </td>
                    <td>
                        <textarea 
                            autocomplete="off" 
                            class="reason"
                            rows="1" 
                            name="compare_plan_reason[]"
                            style="min-width: 280px"
                        ></textarea>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
