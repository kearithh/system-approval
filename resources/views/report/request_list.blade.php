<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">{{ __('Position List') }}</h4>
  </div>
  <div class="table-responsive" style="padding: 0 10px">
    <table class="table">
      <thead class="">
      <th>ល.រ</th>
      <th class="" style="width: 50px">
        {{ __('សកម្មភាព') }}
      </th>
      <th>
        {{ __('ស្ថានភាព') }}
      </th>
      <th style="min-width: 172px;">ថ្ងៃស្នើ</th>
      <th style="min-width: 152px;">
        {{ __('ស្នើសំុដោយ') }}
      </th>
      <th style="min-width: 215px">
        {{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}
      </th>
      <th>
        {{ __('កម្មវត្ថុ') }}
      </th>
      <th style="min-width: 102px">
        {{ __('តម្លៃសរុប') }}
      </th>
      </thead>
      <tbody>
      @foreach($data as $key => $item)
        <tr>
          <td> {{ $key +1  }}</td>
          <td class="td-actions">
            <a href="/request/show?request_token={{ encrypt($item->id) }}" class="btn btn-xs btn-info" title="View the request">
              <i class="fa fa-eye"></i>
            </a>
          </td>
          <td>
            {{ request_status($item) }}
          </td>
          <td>
            {{ created_at($item->created_at) }}
          </td>
          <td>
            {{ $item->requester_name }}
          </td>
          <td>
            {{ reviewer_position(\App\RequestForm::reviewerName($item->id)) }}
          </td>
          <td>
            {{ $item->purpose }}
          </td>
          <td class="text-right">
            {{'$ '. number_format(\App\RequestForm::totalPrice($item->id), 2) }}
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
{!! $data->render() !!}
