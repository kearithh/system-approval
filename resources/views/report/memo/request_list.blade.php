<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">{{ __('Request Memo List') }}</h4>
  </div>
  <div class="table-responsive" style="padding: 0 10px">
    <table class="table">
      <thead class="">
      <th>ល.រ</th>
      <th class="" style="width: 50px">
        {{ __('សកម្ម') }}
      </th>
      <th>
        {{ __('ស្ថានភាព') }}
      </th>
      <th style="min-width: 100px;">ថ្ងៃអនុវត្ត</th>
      <th style="min-width: 90px;">
        {{ __('លេខរៀង') }}
      </th>
      <th style="min-width: 215px">
        {{ __('ចំណងជើង') }}
      </th>
      </thead>
      <tbody>
      @foreach($data as $key => $item)
        <tr>
          <td> {{ $key +1  }}</td>
          <td class="td-actions">
            <a href="/request_memo/{{ ($item->id) }}/pdf" class="btn btn-xs btn-info" title="View the request">
              <i class="fa fa-eye"></i>
            </a>

            @if ($item->status == 2)
                <button class="btn btn-xs btn-success" title="Edit the request" disabled>
                  <i class="fa fa-pen"></i>
                </button>
            @else
              <a href="/request_memo/edit?request_token={{ encrypt($item->id) }}" class="btn btn-xs btn-success" title="Edit the request">
                <i class="fa fa-pen"></i>
              </a>
            @endif

          </td>
          <td>
            {{ memo_status($item) }}
          </td>
          <td>
            {{ start_date($item->start_date) }}
          </td>
          <td>
            {{ $item->no }}
          </td>
          <td>
            {{ $item->title_km }}
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
{!! $data->render() !!}
