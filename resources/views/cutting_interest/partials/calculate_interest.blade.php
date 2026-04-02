<div class="row">
    <div class="col-sm-2 col-form-label">
        <label class="col-form-label">គណនាអត្រាការប្រាក់<span style='color: red'>*</span></label>
    </div> 
    <div class="col-sm-10">
        <div class="table-responsive">
            <table id="sections_cutting" class="table table-hover" style="display: block; overflow-y: auto">
                <thead class="card-header ">
                    <th>ប្រាក់ដើមនៅសល់(៛)<span style='color: red'>*</span></th>
                    <th>ការប្រាក់យល់ព្រមសង(៛)<span style='color: red'>*</span></th>
                    <th>រយៈពេលខ្ចី(ខែ)<span style='color: red'>*</span></th>
                    <th>អត្រាការប្រាក់(%)<span style='color: red'>*</span></th>
                </thead>
                <tbody>
                    <tr class="section_cutting">
                        <td>
                            <input 
                                type="number"
                                id="remain_amount"
                                name="interest_obj[remain_amount]"
                                required
                            >
                        </td>
                        <td>
                            <input 
                                type="number"
                                id="interest_repay"
                                name="interest_obj[interest_repay]"
                                required
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                id="period"
                                name="interest_obj[period]"
                                required
                            >
                        </td>
                        <td>
                            <input 
                                readonly 
                                min="0" 
                                step="0.001" 
                                type="number"
                                id="interest_rate"
                                name="interest_obj[interest_rate]"
                            >
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
