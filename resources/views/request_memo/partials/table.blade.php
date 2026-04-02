<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">{{ __('Memo Request List') }}</h4>
      <div class="text-right">
          <a href="{{ route('request_memo.create') }}" class="btn btn-sm btn-success">{{ __('Create') }}</a>
      </div>
  </div>
  <div class="table-responsive" style="padding: 0 10px">
    <table class="table table-striped">
      <thead class="">
      <th style="min-width: 30px">ល.រ</th>
      <th class="" style="min-width: 120px">
        {{ __('សកម្ម') }}
      </th>
      <th style="min-width: 100px">
        {{ __('ស្ថានភាព') }}
      </th>
      <th class="text-center" style="min-width: 100px;">
        {{ __('អនុសរណៈ') }}
      </th>
      <th style="min-width: 250px">
        {{ __('ចំណងជើង') }}
      </th>
      <th style="min-width: 100px;">
        {{ __('ថ្ងៃអនុវត្ត') }}
      </th>
      <th style="min-width: 180px">
          {{ __('ស្នើដោយ') }}
      </th>
      <th style="min-width: 300px">
        {{ __('ពិនិត្យ និងបញ្ជូនបន្តដោយ') }}
      </th>
      <th style="min-width: 180px">
        {{ __('ថ្ងៃស្នើ') }}
      </th>
      </thead>
      <tbody>
      <?php $i = 1; ?>
      @foreach($data as $key => $item)
        <tr>
          <td> {{ $i++  }}</td>
          <td class="td-actions">

              @include('global.list_action', ['uri' => 'request_memo', 'object' => $item, 'type' => config('app.type_memo')])

          </td>
          <td>
            {{ memo_status($item) }}
          </td>
          <td class="text-center">
            {{ $item->types }}
          </td>
          <td>
            {{ $item->title_km }}
          </td>
          <td>
            {{ start_date($item->start_date) }}
          </td>
          <td>
            {{ $item->requester_name }}
          </td>
          <td>
            {{ reviewer_position(\App\RequestMemo::reviewerName($item->id)) }}
          </td>
          <td>
            {{ created_at($item->created_at) }}
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
{!! $data->render() !!}
