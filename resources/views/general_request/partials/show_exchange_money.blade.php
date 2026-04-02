<div class="body">
 	<span>
 		<!-- គោរពជូនគណនេយ្យហិរញ្ញវត្ថុជាន់ខ្ពស់ជាទីគោរព! -->
 		<?php $approver = @$data->approver(); ?>
 		គោរពជូន{{ @$approver->gender == 'F'? "អ្នកគ្រូ" : "លោកគ្រូ" }}{{ @$approver->position->name_km }}ជាទីគោរព!
 	</span><br>
	<span>
	  	សូមជួយត្រួតពិនិត្យ និងអនុម័តលើសំណើសុំប្តូរប្រាក់ដូចខាងក្រោម៖
	</span>

	<?php @$item = @$data->items->first(); ?>
	<table id="item">
	   <tbody>
	  	 	<tr>
	  			<td>ឈ្មោះសាខា៖</td>
	  			<td> {{ @$data->forbranch->name_km }} </td>
	  		</tr>
	  		<tr>
	  			<td>រូបិយប័ណ្ណប្តូរចេញ៖</td>
	  			<td> {{ @$item->currency_exchange }}</td>
	  		</tr>
	  		<tr>
	  			<td>ទឹកប្រាក់ប្តូរចេញ៖</td>
	  			<td>
	  				@if (@$item->currency_exchange == 'KHR')
	                    {{ number_format(@$item->money_exchange) }} ៛
	                @else
	                    $ {{ number_format(@$item->money_exchange, 2) }}
	                @endif
	  			</td>
	  		</tr>
	  		<tr>
	  			<td>អត្រាប្តូរប្រាក់៖</td>
	  			<td> {{ number_format(@$item->rate) }} </td>
	  		</tr>
	  		<tr>
	  			<td>រូបិយប័ណ្ណប្តូរចូល៖</td>
	  			<td> {{ @$item->currency_remittance }} </td>
	  		</tr>
	  		<tr>
	  			<td>ទឹកប្រាក់ប្តូរចូល៖</td>
	  			<td>
	  				@if (@$item->currency_remittance == 'KHR')
	                    {{ number_format(@$item->money_remittance) }} ៛
	                @else
	                    $ {{ number_format(@$item->money_remittance, 2) }}
	                @endif
	  			</td>
	  		</tr>
	  		<tr>
	  			<td>គោលបំណង៖</td>
	  			<td> {{ $data->purpose }} </td>
	  		</tr>
	    </tbody>
	</table>

	<br>

	<div class="desc" style="text-align: justify; padding-bottom: 15px;">
	   	<p>
	       	សូមអរគុណ!
	       	<span style="float: right;">
              	@foreach($data->reviewers_short() as $key => $value)
                	@if ($value->approve_status == config('app.approve_status_approve'))
                  		<img src="{{ asset($value->short_signature) }}"  
	                        alt="short_sign" 
	                        title="{{ @$value->name }}" 
	                        style="width: 25px;">
                	@endif
             	@endforeach
            </span>
	   	</p>
	</div>

</div>