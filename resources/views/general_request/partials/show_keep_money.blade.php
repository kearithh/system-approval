<div class="body">
	<ul>
		<li>
		    <u><b>សាខា</b></u>៖ {{ @$data->forbranch->name_km }}
		</li>
		<li>
		    <u><b>កម្មវត្ថុ</b></u>៖ {{ $data->purpose }}
		</li>
		<li>
		    <u><b>មូលហេតុ</b></u>៖ {{ $data->reason }}
		</li>
	</ul>
	<span>
	  	<!-- តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើសូមអ្នកគ្រូគណនេយ្យហិរញ្ញវត្ថុជាន់ខ្ពស់មេត្តាអនុញ្ញាតដោយក្ដីអនុគ្រោះ។ -->
	  	<?php $approver = @$data->approver(); ?>
 		តបតាមកម្មវត្ថុ និងមូលហេតុខាងលើសូម{{ @$approver->gender == 'F'? "អ្នកគ្រូ" : "លោកគ្រូ" }}{{ @$approver->position->name_km }}មេត្តាអនុញ្ញាតដោយក្ដីអនុគ្រោះ។
	</span>

	<table id="item">
	  <thead>
	    <tr>
	        <td>ល.រ</td>
	        <td>ប្រភេទ</td>
	        <td style="width: 200px">សាច់ប្រាក់អតិបរិមាដែលអាចរក្សារទុកបាន</td>
	        <td>សាច់ប្រាក់ជាក់ស្ដែង</td>
	        <td>សាច់ប្រាក់លើស</td>
	    </tr>
	  </thead>
	  <tbody>
	    <?php $total = 0; ?>
	    @foreach($data->items as $key => $item)
	      <?php $total += $item->qty; ?>
	        <tr>
	            <td style="text-align: center;">{{ $key +1 }}</td>
	            <td>{{ $item->name }}</td>
	            <td class="text-left">
	                @if ($item->currency=='KHR')
	                    {{ number_format($item->min_money) }} ៛
	                @else
	                    $ {{ number_format($item->min_money, 2) }}
	                @endif
	            </td>
	            <td class="text-left">
	                @if ($item->currency=='KHR')
	                    {{ number_format($item->current_money) }} ៛
	                @else
	                    $ {{ number_format($item->current_money, 2) }}
	                @endif
	            </td>
	            <td class="text-left">
	                @if ($item->currency=='KHR')
	                    {{ number_format($item->excess_money) }} ៛
	                @else
	                    $ {{ number_format($item->excess_money, 2) }}
	                @endif
	            </td>
	        </tr>
	    @endforeach
	    <tr style="font-weight: 700">
	        <td class="text-right" colspan="4">សរុប</td>
	        <td class="text-left">
	            @if ($data->total_amount_usd > 0 )
	                {{'$ '. number_format(($data->total_amount_usd),2) }}
	            @endif
	          @if ($data->total_amount_usd > 0 && $data->total_amount_khr > 0)
	              <br>
	          @endif
	            @if ($data->total_amount_khr > 0 )
	                {{ number_format($data->total_amount_khr) .' ៛'}}
	            @endif
	        </td>
	    </tr>
	  </tbody>
	</table>

	<br>

	<div class="desc" style="text-align: justify; padding-bottom: 15px;">
	  	<p>
	      	{!! $data->desc !!}
	  	</p>
	  	<p>
	      	សូមទទួលនូវសេចក្ដីគោរពដ៏ខ្ពង់ខ្ពស់ពី{{ prifixGender($data->requester()->gender) }}។
	  	</p>
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