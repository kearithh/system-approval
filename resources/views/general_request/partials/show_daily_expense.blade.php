<div class="body">
	<span>សាខា៖
		{{ @$data->forbranch->name_km }}
	</span><br>
 	<span>
 		<!-- គោរពជូនអ្នកគ្រូគណនេយ្យហិរញ្ញវត្ថុជាន់ខ្ពស់! -->
 		<?php $approver = @$data->approver(); ?>
 		គោរពជូន{{ @$approver->gender == 'F'? "អ្នកគ្រូ" : "លោកគ្រូ" }}{{ @$approver->position->name_km }}ជាទីគោរព!
 	</span><br>
	<span>
		@if (@$data->type == 4)
	  		សូមជួយត្រួតពិនិត្យ និងអនុម័តលើកិច្ចបញ្ជីការគណនេយ្យសម្រាប់ប្រតិបត្តិការចំណាយដូចខាងក្រោម៖
	  	@else
	  		សូមជួយត្រួតពិនិត្យ និងអនុម័តលើកិច្ចបញ្ជីការគណនេយ្យសម្រាប់ប្រតិបត្តិការដូចខាងក្រោម៖
	  	@endif
	</span>

	<table id="item">
	  	<thead>
	    	<tr>
		        <td>Acc No</td>
		        <td>Account Name</td>
		        <td>Description</td>
		        <td style="min-width: 120px">Debit</td>
		        <td style="min-width: 120px">Credit</td>
	    	</tr>
	  	</thead>
	  	<tbody>
		    <?php $total_debit = 0; ?>
		    <?php $total_credit = 0; ?>
		    <?php $total_debit_khr = 0; ?>
		    <?php $total_credit_khr = 0; ?>
		    @foreach($data->items as $key => $item)
		        <tr>
		            <td style="text-align: center;">{{ $item->no }}</td>
		            <td>{{ $item->name }}</td>
		            <td class="text-left">{{ $item->descrip }}</td>
		            <td class="text-left">
		                @if ($item->currency=='KHR')
		                    {{ number_format($item->debit) }} ៛
		                    <?php $total_debit_khr += $item->debit; ?>
		                @else
		                    $ {{ number_format($item->debit, 2) }}
		                    <?php $total_debit += $item->debit; ?>
		                @endif
		            </td>
		            <td class="text-left">
		                @if ($item->currency=='KHR')
		                    {{ number_format($item->credit) }} ៛
		                    <?php $total_credit_khr += $item->credit; ?>
		                @else
		                    $ {{ number_format($item->credit, 2) }}
		                    <?php $total_credit += $item->credit; ?>
		                @endif
		            </td>
		        </tr>
		    @endforeach
		    <tr style="font-weight: 700">
		        <td class="text-right" colspan="3">សរុប</td>
		        <td class="text-left">
		            @if ($total_debit > 0 )
		                {{'$ '. number_format(($total_debit),2) }}
		            @endif
		          	@if ($total_debit > 0 && $total_debit_khr > 0)
		              	<br>
		          	@endif
		            @if ($total_debit_khr > 0 )
		                {{ number_format($total_debit_khr) .' ៛'}}
		            @endif
		        </td>
		        <td class="text-left">
		            @if ($total_credit > 0 )
		                {{'$ '. number_format(($total_credit),2) }}
		            @endif
		          	@if ($total_credit > 0 && $total_credit_khr > 0)
		              	<br>
		          	@endif
		            @if ($total_credit_khr > 0 )
		                {{ number_format($total_credit_khr) .' ៛'}}
		            @endif
		        </td>
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
                  		<img  src="{{ asset($value->short_signature) }}"  
	                        alt="short_sign" 
	                        title="{{ @$value->name }}" 
	                        style="width: 25px;">
                	@endif
             	@endforeach
            </span>
	  	</p>
	</div>

</div>