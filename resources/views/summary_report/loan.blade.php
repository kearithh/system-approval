@extends('adminlte::page', ['activePage' => 'request', 'titlePage' => __('Request')])

@section('plugins.Select2', true)

@section('css')
  <style>
    .table td {
      vertical-align: middle;
    }
  </style>
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <a class="btn btn-xs bg-success" href="/summary_report/loan? status={{ config('app.approve_status_approve') }}" style="font-size: 0.85rem;">
                Total Approved
                <span class="badge badge-light">{{ @$totalApproved }}</span>
              </a>
              <a class="btn btn-xs bg-orange" href="/summary_report/loan? status={{ config('app.approve_status_draft') }}" style="font-size: 0.85rem;">
                Total Pending
                <span class="badge badge-light">{{ @$totalPending }}</span>
              </a>
              <a class="btn btn-xs bg-danger" href="/summary_report/loan? status={{ config('app.approve_status_reject') }}" style="font-size: 0.85rem;">
                Total Commented
                <span class="badge badge-light">{{ @$totalCommented }}</span>
              </a>
              <a class="btn btn-xs bg-dark" href="/summary_report/loan? status={{ config('app.approve_status_disable') }}" style="font-size: 0.85rem;">
                Total Rejected
                <span class="badge badge-light">{{ @$totalRejected }}</span>
              </a>
              <a class="btn btn-xs bg-secondary" href="/summary_report/loan? status={{ config('app.approve_status_delete') }}" style="font-size: 0.85rem;">
                Total Deleted
                <span class="badge badge-light">{{ @$totalDeleted }}</span>
              </a>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @include('global.search_request',['clear_url' => 'loan', 'branch_request' => '', 'department_request' => 'hidden', 'company_request' => [1, 4, 5, 6, 7, 8]])
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title "><strong>Loan Approval List</strong></h4>
              <div class="col-sm-12 text-right">
                <button class="btn btn-outline-secondary btn-sm">Total: {{ $total }} Records</button>
              </div>
            </div>
            <div class="table-responsive" style="padding: 0 10px">
              <table class="table table-striped table-hover">
                <thead class="">
                  <tr>
                    <th style="min-width: 50px">ល.រ</th>
                    <th>មើល</th>
                    <th style="min-width: 100px;">{{ __('ស្ថានភាព') }}</th>
                    <th style="min-width: 200px;">{{ __('កាលបរិច្ឆេទស្នើរ') }}</th>
                    <th style="min-width: 200px;">{{ __('កាលបរិច្ឆេទកែសម្រួល') }}</th>
                    <th style="min-width: 200px;">{{ __('ស្នើរដោយ') }}</th>
                    <th style="min-width: 245px;">{{ __('ត្រួតពិនិត្យដោយ') }}</th>
                    <th style="min-width: 245px;">{{ __('អនុម័តដោយ') }}</th>
                    <th style="min-width: 245px;">{{ __('ចម្លងជូន') }}</th>
                    <th style="min-width: 152px;">{{ __('ក្រុមហ៊ុន') }}</th>
                    <th style="min-width: 130px;">{{ __('ឈ្មោះសាខា') }}</th>
                    <th style="min-width: 200px;">{{ __('ឈ្មោះមន្រ្តីឥណទាន') }}</th>
                    <th style="min-width: 150px;">{{ __('ឈ្មោះអ្នកខ្ចី') }}</th>
                    <th style="min-width: 150px;">{{ __('ឈ្មោះអ្នករួមខ្ចី') }}</th>
                    <th style="min-width: 170px; text-align: center;">{{ __('ទំហំឥណទាន(រៀល)') }}</th>
                    <th style="min-width: 150px; text-align: center;">រយៈពេលខ្ចី <br> (ខែ/សប្តាហ៍)</th>
                    <th style="min-width: 150px; text-align: center;">អត្រាការប្រាក់(%)</th>
                    <th style="min-width: 150px; text-align: center;">សេវារដ្ឋបាល(%)</th>

                    <th style="min-width: 150px; text-align: center;">សេវារៀបចំឥណទាន(%)</th>
                    <th style="min-width: 150px; text-align: center;">សេវាត្រួតពិនិត្យឥណទាន(%)</th>
                    <th style="min-width: 150px; text-align: center;">សេវាប្រមូលឥណទាន(%)</th>

                    <th style="min-width: 300px;">{{ __('របៀបសង') }}</th>
                    <th style="min-width: 200px;">របៀបអនុម័ត</th>
                    <th style="min-width: 200px;">ប្រភេទឥណទាន</th>
                    <th style="min-width: 200px;">តំណភ្ជាប់ទីតាំង(Map)</th>
                  </tr>
                </thead>
                <tbody>
                  @if($data->count())
                    <?php $i = 1; ?>
                    @foreach($data as $key => $item)
                        <tr title="Request ID: {{$item->id}}">
                          <td>{{ $i++  }}</td>
                          <td>
                            <a  href="{{ route('loan.show', $item->id) }}" 
                                class="preview btn btn-xs btn-info" 
                                title="View the request">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                          <td>
                              @if ($item->deleted_at)
                                  <button class="btn btn-xs bg-dark" title="Request was Deleted" >Deleted</button>
                              @else
                                  {{ request_status($item) }}
                              @endif
                          </td>
                          <td>{{ created_at($item->created_at) }}</td>
                          <td>{{ $item->resubmit ? created_at($item->resubmit) : 'N/A' }}</td>
                          <td>{{ $item->requester_name }}</td>
                          <td>
                              {{ @reviewer_position(\App\Loan::reviewerNames($item->id)) }}
                          </td>
                          <td>
                              {{ @(\App\Loan::approverName($item->id)->first()->reviewer_name) }}
                          </td>
                          <td>
                              {{ @reviewer_position(\App\Loan::ccNames($item->id)) }}
                          </td>
                          <td>{{ $item->company_name }}</td>
                          <td>{{ $item->branch_name }}</td>
                          <td>{{ $item->credit }}</td>
                          <td>{{ $item->borrower }}</td>
                          <td>
                              <?php
                                  $part = json_decode($item->participants);
                              ?>
                                  {{ $part ? implode(", ",$part) : 'N/A' }}
                          </td>
                          <td class="text-right">{{ number_format($item->money) }}៛</td>
                          <td class="text-center">
                            {{ $item->times }}
                            @if($item->type_time == 1)
                              ខែ
                            @else
                              សប្តាហ៍
                            @endif
                          </td>
                          <td class="text-center">{{ $item->interest }}%</td>

                          <td class="text-center">
                              {{ $item->service ? @$item->service : 0}}%
                          </td>

                          <?php $obj = json_decode(@$item->service_object); ?>
                          <td class="text-center">
                              {{ @$obj->arrangement ? @$obj->arrangement : 0 }}%
                          </td>
                          <td class="text-center">
                              {{ @$obj->check ? @$obj->check : 0 }}%
                          </td>
                          <td class="text-center">
                              {{ @$obj->collection ? @$obj->collection : 0 }}%
                          </td>

                          <td>
                              
                              @if($item->types == 1) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ១សប្តាហ៍ម្តង
                              @elseif($item->types == 2) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ២សប្តាហ៍ម្តង
                              @elseif($item->types == 3) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ខែ
                              @elseif($item->types == 4) 
                                  សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                              @elseif($item->types == 5) 
                                  សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                              @elseif($item->types == 6) 
                                  សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                              @elseif($item->types == 7) 
                                  សងការប្រាក់រាល់ខែ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                              @elseif($item->types == 8) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ៤ខែម្តង
                              @elseif($item->types == 9) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ៦ខែម្តង
                              @elseif($item->types == 10) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ៨ខែម្តង
                              @elseif($item->types == 11) 
                                  សងការប្រាក់ និងប្រាក់ដើមរាល់ ១២ខែម្តង
                              @endif

                          </td>
                          <td>
                              @if($item->principle == 1)
                                  អនុម័តតាមគោលការណ៍
                              @else($item->principle == 0)
                                  អនុម័តខុសគោលការណ៍
                              @endif
                          </td>
                          <td>
                              @if ($item->type_loan == 1)
                                ឥណទានថ្មី
                              @elseif ($item->type_loan == 3)
                                ឥណទានចាស់
                              @elseif ($item->type_loan == 2)
                                ឥណទានរៀបចំឡើងវិញ
                              @elseif ($item->type_loan == 4)
                                ឥណទានរៀបចំឡើងវិញលើកទី១
                              @elseif ($item->type_loan == 5)
                                ឥណទានរៀបចំឡើងវិញលើកទី២
                              @elseif ($item->type_loan == 6)
                                ឥណទានរៀបចំឡើងវិញលើកទី៣
                              @elseif ($item->type_loan == 7)
                                ឥណទានរៀបចំឡើងវិញលើកទី៤
                              @elseif ($item->type_loan == 8)
                                ឥណទានរៀបចំឡើងវិញលើកទី៥
                              @elseif ($item->type_loan == 9)
                                ឥណទានរៀបចំឡើងវិញលើកទី៦
                              @elseif ($item->type_loan == 10)
                                ឥណទានរៀបចំឡើងវិញលើកទី៧
                              @elseif ($item->type_loan == 11)
                                ឥណទានរៀបចំឡើងវិញលើកទី៨
                              @elseif ($item->type_loan == 12)
                                ឥណទានរៀបចំឡើងវិញលើកទី៩
                              @elseif ($item->type_loan == 13)
                                ឥណទានរៀបចំឡើងវិញលើកទី១០
                              @else
                                  ឥណទានថ្មី
                              @endif
                          </td>
                          
                          <td class="text-left">
                              <?php 
                                $gps_object = @json_decode(@$item->gps_object) ?: []; 
                              ?>
                              @forelse(@$gps_object as $key => $item)
                                <a href="{{ $item->link }}">{{ $key + 1 }}. {{ $item->name }}</a><br>
                              @empty
                                N/A
                              @endforelse
                          </td>

                        </tr>
                      @endforeach
                    @else
                    <tr>
                      <td colspan="24">Record Not Found!</td>
                    </tr>
                  @endif
                </tbody>
              </table>
              {{ $data->appends($_GET)->links() }}
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

