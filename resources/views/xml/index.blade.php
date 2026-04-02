@extends('adminlte::page', ['activePage' => 'xml', 'titlePage' => __('XML Management')])
@push('css')
@endpush
@section('plugins.Select2', true)

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <form action="{{ route('xml.update') }}" method="POST">
                  @csrf
                  <button class="btn btn-success btn-sm mb-3">Update AML</button>
              </form>

              <label for="">
                  ទិន្ន័យបានទាញយកចុងក្រោយនៅថ្ងៃទី
                  <span class="text-success">{{ $lastUpdate }}</span>
                  , Individuals: <span class="text-danger">{{ $totalIndividual }}</span>
                  , Entities: <span class="text-danger">{{ $totalEntity }}</span>

              </label>
          </div>
          <div class="col-sm-12">
              <form action="">
                  <div class="form-group mb-1">
                      <input type="text" class="form-control" id="" name="keyword" placeholder="Name, NID, Passport, Keyword" value="{{ @$_GET['keyword'] }}">
                  </div>
                  <a href="{{ route('xml.index') }}" class="btn btn-sm btn-default">Reset</a>
                  <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
              </form>
          </div>

          <div class="col-md-12">
              <div class="card">
                  <div class="table-responsive" style="padding: 0 10px; ">
                      <table class="table table-striped table-hover">
                          <thead class="">
                            <th style="min-width: 100px">
                                {{ __('No') }}
                            </th>
                            <th style="min-width: 400px">
                                {{ __('Name') }}
                            </th>
                            <th style="min-width: 600px">
                                {{ __('National Identification | Passport') }}
                            </th>
                            <th style="min-width: 300px">
                                {{ __('Nationality') }}
                            </th>
                            <!-- <th style="min-width: 150px">
                                {{ __('Object') }}
                            </th> -->
                            <th style="min-width: 150px">
                                {{ __('DOB') }}
                            </th>
                            <!-- <th style="min-width: 150px">
                                {{ __('Object') }}
                            </th> -->
                          </thead>
                          <tbody>
                          @if (isset($data))
                              @foreach($data as $key => $item)
                                  <tr>
                                      <td>
                                          {{ $key + 1 }}
                                      </td>
                                      <td>
                                          {{ @$item->FIRST_NAME }} {{ @$item ->SECOND_NAME }}
                                      </td>
                                      <td>

                                          <?php $types = @json_decode($item->INDIVIDUAL_DOCUMENT) ?>

                                          @if(@$types->TYPE_OF_DOCUMENT)
                                              {{ @$types->TYPE_OF_DOCUMENT . ':' }}
                                              {{ @$types->NUMBER }} 
                                          @endif

                                          @if(@$item->INDIVIDUAL_DOCUMENT)
                                              <?php $doc = is_array($item->INDIVIDUAL_DOCUMENT) ? $item->INDIVIDUAL_DOCUMENT : json_decode($item->INDIVIDUAL_DOCUMENT); ?> 
                                              @foreach($doc as $nid)
                                                  @if(@$nid->TYPE_OF_DOCUMENT)
                                                      {{ @$nid->TYPE_OF_DOCUMENT . ':'}}
                                                      {{ @$nid->NUMBER }} <br>
                                                  @endif
                                              @endforeach
                                          @endif

                                      </td>
                                      <!-- <td>
                                          {{ @$item ->INDIVIDUAL_DOCUMENT }}
                                      </td> -->

                                      <td>
                                          <?php $national = @json_decode($item->NATIONALITY) ?>
                                          @if (is_array(@$national->VALUE))
                                              @foreach(@$national->VALUE as $ni)
                                                  {{ @$ni }} <br>
                                              @endforeach
                                          @else
                                              {{ @$national->VALUE }}
                                          @endif
                                      </td>

                                      <td>

                                          <?php $dob = @json_decode($item->INDIVIDUAL_DATE_OF_BIRTH) ?>

                                          @if(@$dob->DATE)
                                              {{ @$dob->DATE }}
                                          @endif

                                          @if(@$item->INDIVIDUAL_DATE_OF_BIRTH)
                                              <?php $doc = is_array($item->INDIVIDUAL_DATE_OF_BIRTH) ? $item->INDIVIDUAL_DATE_OF_BIRTH : json_decode($item->INDIVIDUAL_DATE_OF_BIRTH); ?> 
                                              @foreach($doc as $dates)
                                                  @if(@$dates->DATE)
                                                      {{ @$dates->DATE }} <br>
                                                  @endif
                                              @endforeach
                                          @endif

                                      </td>

                                      <!-- <td>
                                          {{ @$item->INDIVIDUAL_DATE_OF_BIRTH }}
                                      </td> -->
                                  </tr>
                              @endforeach
                          @else
                              <tr>
                                  <td colspan="4" class="text-center">
                                      <span class="text-danger">Not found</span>
                                  </td>
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
  </div>
@endsection

@push('js')
  <script src="{{ asset('js/sweetalert2@9.js') }}"></script>
  <script>
    $(document).ready(function () {

    });
  </script>
@endpush
