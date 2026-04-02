<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">{{ __('Dispose Request List') }}</h4>
      <div class="text-right">
          <a href="{{ route('request_dispose.create') }}" class="btn btn-sm btn-success">{{ __('Create') }}</a>
      </div>
  </div>
  <div class="table-responsive" style="padding: 0 10px">
    <table class="table">
      <thead class="">
      <th style="min-width: 50px">​ល.រ</th>
      <th class="" style="min-width: 100px">
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
      <th style="min-width: 150px">
          {{ __('ថ្ងៃស្នើរ') }}
      </th>
      </thead>
      <tbody>
      @foreach($data as $key => $item)
        <tr>
          <td> {{ $key +1  }}</td>
          <td class="td-actions">
{{--            <a href="{{ route('request_dispose.show', $item->id) }}" class="btn btn-xs btn-info" title="View the request">--}}
{{--              <i class="fa fa-eye"></i>--}}
{{--            </a>--}}
{{--            @if ($item->status != 1)--}}
{{--              <button class="btn btn-xs btn-success" title="Edit the request" disabled>--}}
{{--                <i class="fa fa-pen"></i>--}}
{{--              </button>--}}
{{--              <button class="btn btn-xs btn-danger" title="Delete the request" disabled>--}}
{{--                <i class="fa fa-trash"></i>--}}
{{--              </button>--}}
{{--            @else--}}
{{--              <a href="{{ route('request_dispose.edit', $item->id) }}" class="btn btn-xs btn-success" title="Edit the request">--}}
{{--                <i class="fa fa-pen"></i>--}}
{{--              </a>--}}

{{--              <form class="delete_form" method="GET" action="{{ action('RequestDisposeController@destroy', $item->id) }}" style="display: inline-block">--}}
{{--                  <button class="btn btn-xs btn-danger" name="id" value="{{$item->id }}" title="delete the request">--}}
{{--                      @csrf--}}
{{--                      <i class="fa fa-trash"></i>--}}
{{--                  </button>--}}
{{--              </form>--}}
{{--            @endif--}}
              @include('global.list_action', ['uri' => 'request_dispose', 'object' => $item])

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
              {{ reviewer_position(\App\RequestDispose::reviewerName($item->id)) }}
          </td>
          <td>
              {{ $item->created_at }}
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
{!! $data->render() !!}

@push('js')
    <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
    <script>
        @if(session('status'))
        Swal.fire({
            title: 'Success',
            icon: 'success',
            timer: '2000',
        })
        @endif
    </script>
    <script>
    $(document).ready(function() {

        $("form.delete_form" ).on( "click", function( event ) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $(this).submit();
                }
            })
        });
    });
</script>
@endpush



