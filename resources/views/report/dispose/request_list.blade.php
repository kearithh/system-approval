<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">{{ __('Request Dispose List') }}</h4>
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
      <th style="min-width: 100px;">បរិយាយ</th>
      <th style="min-width: 90px;">
        {{ __('សំណង') }}
      </th>
      <th style="min-width: 215px">
        {{ __('ហេតុផល') }}
      </th>
      <th style="min-width: 215px">
          {{ __('ពិនិត្យដោយ') }}
      </th>
      </thead>
      <tbody>
      @foreach($data as $key => $item)
        <tr>
          <td> {{ $key +1  }}</td>
          <td class="td-actions">
            <a href="/request_dispose/{{ ($item->id) }}/pdf" class="btn btn-xs btn-info" title="View the request">
              <i class="fa fa-eye"></i>
            </a>

            @if ($item->status == 2)
                <button class="btn btn-xs btn-success" title="Edit the request" disabled>
                  <i class="fa fa-pen"></i>
                </button>
            @else
              <a href="{{ route('request_dispose.edit', $item->id) }}" class="btn btn-xs btn-success" title="Edit the request">
                <i class="fa fa-pen"></i>
              </a>
            @endif

          </td>
          <td>
            {{ memo_status($item) }}
          </td>
          <td>
            <pre>{{ $item->desc }}</pre>
          </td>
          <td>
            {{ is_penalty($item->is_penalty) }}
          </td>
          <td>
              @if($item->is_penalty)

              @else
                  <?php $penalty = json_decode($item->penalty); ?>
                  {{ $penalty ? ($penalty->reason) : '' }}
              @endif

          </td>
            <td>
                {{ reviewer_position(\App\RequestForm::reviewerName($item->id)) }}
            </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
{!! $data->render() !!}
