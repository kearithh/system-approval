{{--<div class="card">--}}
{{--  <div class="card-header card-header-primary">--}}
{{--    <h4 class="card-title ">{{ __('Memo Request List') }}</h4>--}}
{{--      <div class="text-right">--}}
{{--          <a href="{{ route('request_memo.create') }}" class="btn btn-sm btn-success">{{ __('Create') }}</a>--}}
{{--      </div>--}}
{{--  </div>--}}
{{--  <div class="table-responsive" style="padding: 0 10px">--}}
{{--    <table class="table">--}}
{{--      <thead class="">--}}
{{--      <th style="width: 30px">ល.រ</th>--}}
{{--      <th class="" style="width: 120px">--}}
{{--        {{ __('សកម្ម') }}--}}
{{--      </th>--}}
{{--      <th style="width: 100px">--}}
{{--        {{ __('ស្ថានភាព') }}--}}
{{--      </th>--}}
{{--      <th style="min-width: 100px;">--}}
{{--        {{ __('ថ្ងៃអនុវត្ត') }}--}}
{{--      </th>--}}
{{--      <th style="min-width: 100px;">--}}
{{--        {{ __('លេខរៀង') }}--}}
{{--      </th>--}}
{{--      <th>--}}
{{--        {{ __('ចំណងជើង') }}--}}
{{--      </th>--}}
{{--      <th style="min-width: 180px">--}}
{{--        {{ __('ថ្ងៃស្នើ') }}--}}
{{--      </th>--}}
{{--      </thead>--}}
{{--      <tbody>--}}
{{--      @foreach($data as $key => $item)--}}
{{--        <tr>--}}
{{--          <td> {{ $key +1  }}</td>--}}
{{--          <td class="td-actions">--}}

{{--            <a href="/request_memo/{{ ($item->id) }}/pdf" class="btn btn-xs btn-info" title="View the request">--}}
{{--              <i class="fa fa-eye"></i>--}}
{{--            </a>--}}

{{--            @if ($item->status == 2)--}}
{{--                <button class="btn btn-xs btn-success" title="Edit the request" disabled>--}}
{{--                  <i class="fa fa-pen"></i>--}}
{{--                </button>--}}
{{--                  <button class="btn btn-xs btn-danger" title="delete the request" disabled>--}}
{{--                      <i class="fa fa-trash"></i>--}}
{{--                  </button>--}}
{{--            @else--}}
{{--              <a href="/request_memo/{{ $item->id }}/edit" class="btn btn-xs btn-success" title="Edit the request">--}}
{{--                <i class="fa fa-pen"></i>--}}
{{--              </a>--}}
{{--                  <form class="delete_form" method="POST" action="{{ action('RequestMemoController@destroy', $item->id) }}" style="display: inline-block">--}}
{{--                      <button class="btn btn-xs btn-danger" name="id" value="{{$item->id }}" title="delete the request">--}}
{{--                          @csrf--}}
{{--                          <i class="fa fa-trash"></i>--}}
{{--                      </button>--}}
{{--                  </form>--}}
{{--            @endif--}}
{{--              @include('global.list_action', ['uri' => 'request_memo', 'object' => $item])--}}

{{--          </td>--}}
{{--          <td>--}}
{{--            {{ memo_status($item) }}--}}
{{--          </td>--}}
{{--          <td>--}}
{{--            {{ start_date($item->start_date) }}--}}
{{--          </td>--}}
{{--          <td>--}}
{{--            {{ $item->no }}--}}
{{--          </td>--}}
{{--          <td>--}}
{{--            {{ $item->title_km }}--}}
{{--          </td>--}}
{{--            <td>--}}
{{--                {{ $item->created_at }}--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--      @endforeach--}}
{{--      </tbody>--}}
{{--    </table>--}}
{{--  </div>--}}
{{--</div>--}}
{{--{!! $data->render() !!}--}}
