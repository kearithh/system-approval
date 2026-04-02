@extends('adminlte::page', ['activePage' => 'Contract', 'titlePage' => __('Contract')])

@section('content')
<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <form action="">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group mb-1">
                            <input type="text" class="form-control" id="" name="keyword" placeholder="keyword" value="{{ @$_GET['keyword'] }}">
                        </div>
                    </div>
                </div>
                <a href="{{ route('contract') }}" class="btn btn-sm btn-default">Reset</a>
                <button type="submit" class="btn btn-sm btn-primary m-2">Search</button>
            </form>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><strong>Contract List</strong></h4>
                        <div class="text-right">
                            <button @click="showData()" class="float-right btn btn-sm btn-outline-success">Create</button>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive" style="padding: 0 10px">
                    <table class="table table-striped">
                      <thead class="">
                        <th class="text-center" >ល.រ</th>
                        <th class="text-nowrap">សកម្មភាព</th>
                        <th class="text-nowrap">ម្ចាស់ទ្រព្យសម្បត្តិ</th>
                        <th class="text-nowrap">ទ្រព្យសម្បត្តិ</th>
                        <th class="text-nowrap ">ឯកសារ</th>
                        <th class="text-nowrap">ប្រភេទកុងត្រា</th>
                        <th class="text-nowrap">ចំនួនទឹកប្រាក់($)</th>
                        <th class="text-nowrap">ចំនួនទឹកប្រាក់នៅសល់($) </th>
                        <th class="text-nowrap">ថ្ងៃចាប់ផ្ដើមអនុវត្ត </th>
                        <th class="text-nowrap">ថ្ងៃផុតកំណត់ </th>
                        <th class="text-nowrap">បរិយាយ</th>
                        <th class="text-nowrap">បង្កើតដោយ </th>
                        <th class="text-nowrap">កែប្រែដោយ </th>
                    </thead>
                    <tbody>
                        @foreach(@$contracts ?? [] as $key => $value)
                        <?php
                            $obj           = json_decode(@$value->data);
                            $pro_json      = @$value->propertiesName;
                            $property      = json_decode(@$pro_json->data);
                            $pro_name      = @$property->name;
                            $proper        = @$value->propertiesName;
                            $contract_type = Constants::PROPERTY_TYPE[@$obj->contract_type];
                        ?>
                        <tr>
                            <td class="text-center">
                                {{ $key + 1 }}
                            </td>
                            <td class="td-actions text-nowrap text-center">
                                @if ($obj->remaining_amount )
                                    <a rel="tooltip" class="btn btn-info btn-xs" @click="payment({{ $value }})" data-original-title="" title="Payment">
                                        <i class="fa fa-credit-card"></i>
                                    </a>
                                @endif
                                <a rel="tooltip" class="btn btn-primary btn-xs" @click="showpayment( {{ $value->id }})" data-original-title="" title="Show Payment" >
                                    <i class="fa fa-eye"></i>
                                </a>
                                |
                                @if (@$obj->full_amount == @$obj->remaining_amount)
                                    <a rel="tooltip" class="btn btn-success btn-xs" @click="showData({{ $value }})" data-original-title="" title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                @endif
                                <a rel="tooltip" class="btn btn-info btn-xs" @click="showhistory( {{ $value->id }})" data-original-title="" title="Show History" >
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a onclick="return confirm('Are you sure you want to delete this position?')" href="{{ route('contract.destroy', $value->id) }}" class="btn btn-xs btn-danger" title="Delete the request">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td class="text-nowrap">{{ @$proper->proOwner->name }}</td>
                            <td class="text-nowrap">{{ @$pro_name }}</td>
                            <td class="text-nowrap">
                                <a href="{{url("/uploads/$obj->attachfile")}}" target="_blank" class="open_link_file">
                                    {{ $obj->attachfile }}
                                </a>
                            </td>
                            <td class="text-nowrap">{{ @$contract_type }}</td>
                            <td class="text-nowrap">{{ @$obj->full_amount }}</td>
                            <td class="text-nowrap">{{ @$obj->remaining_amount }}</td>
                            <td class="text-nowrap">{{ @$obj->effective_date }}</td>
                            <td class="text-nowrap">{{ @$obj->due_date }}</td>
                            <td class="text-nowrap">{{ @$obj->description }}</td>
                            <td class="text-nowrap">
                            {{ @$value->userCreated->username }}
                            </td>
                            <td class="text-nowrap">
                                {{ @$value->userUpdateBy->username }}
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    {{ @$contracts->appends($_GET)->links() }}
                  </div>
                <form method="post"
                            action="{{ route('contract-store') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Constract</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form>
                                <input type="hidden" name="id">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class=" col-form-label">{{ __('Properties') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('properties_id') ? ' has-danger' : '' }}">
                                        <select class="form-control myselect2" name="properties_id" required>
                                            {{-- <option value="">---</option> --}}
                                            @foreach(@$properties ?? [] as $item)
                                               <?php
                                                    $obj = json_decode($item->data);
                                               ?>
                                              <option value="{{ $item->id }}">{{ $obj->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('properties_id'))
                                            <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('properties_id') }}</span>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label">{{ __('ប្រភេទកុងត្រា') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('contract_type') ? ' has-danger' : '' }}">
                                        <select class="form-control" name="contract_type"
                                            required="true"
                                            aria-required="true"
                                        >
                                        <option value=""> << Please select >> </option>
                                        @foreach (Constants::PROPERTY_TYPE as $key => $value)
                                          <option value="{{ $key }}"> {{ $value }} </option>
                                        @endforeach
                                        </select>
                                        @if ($errors->has('contract_type'))
                                            <span id="name-type" class="error text-danger" for="input-name">{{ $errors->first('contract_type') }}</span>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class=" col-form-label">{{ __('ថ្ងៃចាប់ផ្ដើមអនុវត្ត') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('effective_date') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="effective_date"
                                                class="datepicker form-control {{ $errors->has('effective_date') ? ' is-invalid' : '' }}"
                                                name="effective_date"
                                                required
                                                value="{{ old('effective_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                                                data-inputmask-inputformat="dd-mm-yyyy"
                                                placeholder="dd-mm-yyyy"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class=" col-form-label">{{ __('ថ្ងៃផុតកំណត់') }}<span style='color: red'>*</span></label>
                                        <div class="form-group{{ $errors->has('due_date') ? ' has-danger' : '' }}">
                                            <input
                                                type="text"
                                                id="due_date"
                                                class="datepicker form-control {{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                name="due_date"
                                                required
                                                value="{{ old('due_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                                                data-inputmask-inputformat="dd-mm-yyyy"
                                                placeholder="dd-mm-yyyy"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">ចំនួនទឹកប្រាក់($)<span style='color: red'>*</span></label>
                                           <input
                                              class="form-control{{ $errors->has('full_amount') ? ' is-invalid' : '' }}"
                                              name="full_amount"
                                              id="full_amount"
                                              type="number"
                                              placeholder="{{ __('full amount') }}"
                                              value="{{ old('full_amount') }}"
                                              required="true"
                                              aria-required="true"
                                          />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label" for="input-password-confirmation">{{ __('ឯកសារ') }}</label>
                                        <div class="col-sm-6">
                                          <div class="form-group form-file-upload {{ $errors->has('attachfile') ? ' has-danger' : '' }}">
                                            <input name="attachfile" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                                          </div>
                                          @if ($errors->has('attachfile'))
                                            <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('attachfile') }}</span>
                                          @endif
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">ចំនួនទឹកប្រាក់នៅសល់</label>
                                           <input
                                              class="form-control"
                                              name="remaining_amount"
                                              id="remaining_amount"
                                              type="number"
                                              placeholder="{{ __('remaining amount') }}"
                                              value="{{ old('remaining_amount') }}"
                                          />
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="message-text" class="col-form-label">បរិយាយ(Description)</label>
                                            <textarea style="height: 110px;"
                                                  class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                                  name="description"
                                                  id="description"
                                              >
                                              </textarea>
                                          </div>
                                    </div>


                                </div>

                              </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                          </div>
                        </div>
                      </div>
                </form>
                {{-- payment --}}
                <form method="post"
                            action="{{ route('contract-payment') }}"
                            enctype="multipart/form-data"
                            autocomplete="off"
                            class="form-horizontal">
                    @csrf
                    @method('post')
                    <div class="modal fade" id="payment" tabindex="-1" role="dialog" aria-labelledby="paymentLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="paymentLabel">Payment</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <input type="hidden" name="id">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="recipient-name" class="col-form-label">ចំនួនទឹកប្រាក់ត្រូវបង់($)</label>
                                               <input
                                                  class="form-control"
                                                  name="full_amount"
                                                  id="full_amount"
                                                  type="number"
                                                  placeholder="{{ __('full amount') }}"
                                                  value="{{ old('full_amount') }}"
                                                  readonly
                                              />
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="recipient-name" class="col-form-label">ចំនួនទឹកប្រាក់បង់($)<span style='color: red'>*</span></label>
                                               <input
                                                  class="form-control"
                                                  name="paid_amount"
                                                  id="paid_amount"
                                                  type="number"
                                                  placeholder="{{ __('Paid amount') }}"
                                                  value="{{ old('paid_amount') }}"
                                                  required="true"
                                                  aria-required="true"

                                              />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label class=" col-form-label">{{ __('បង់នៅថ្ងៃ') }}<span style='color: red'>*</span></label>
                                            <div class="form-group{{ $errors->has('paid_date') ? ' has-danger' : '' }}">
                                                <input
                                                    type="text"
                                                    id="paid_date"
                                                    class="datepicker form-control {{ $errors->has('paid_date') ? ' is-invalid' : '' }}"
                                                    name="paid_date"
                                                    required
                                                    value="{{ old('paid_date', \Carbon\Carbon::now()->format('d-m-Y')) }}"
                                                    data-inputmask-inputformat="dd-mm-yyyy"
                                                    placeholder="dd-mm-yyyy"
                                                    autocomplete="off"
                                                >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="recipient-name" class="col-form-label">ចំនួនទឹកប្រាក់នៅសល់($)</label>
                                               <input
                                                  class="form-control"
                                                  name="remaining_amount"
                                                  id="remaining_amount"
                                                  type="number"
                                                  value="{{ old('remaining_amount') }}"
                                                  readonly
                                              />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="message-text" class="col-form-label">បរិយាយ(Description)</label>
                                                <textarea style="height: 110px;"
                                                      class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                                      name="description"
                                                      id="description"
                                                  >
                                                  </textarea>
                                              </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="col-form-label" for="input-password-confirmation">{{ __('ឯកសារ') }}</label>
                                            <div class="col-sm-6">
                                              <div class="form-group form-file-upload {{ $errors->has('attachfile') ? ' has-danger' : '' }}">
                                                <input name="attachfile" type="file" class="" style="z-index: 1; opacity: 1; height: 28px">
                                              </div>
                                              @if ($errors->has('attachfile'))
                                                <br><span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('attachfile') }}</span>
                                              @endif
                                            </div>
                                        </div>

                                    </div>

                                  </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                          </div>
                        </div>
                      </div>
                </form>
                {{-- show payment --}}
                <div class="modal fade" id="showpayment" tabindex="-1" role="dialog" aria-labelledby="paymentLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content printableArea">
                            <div class="" style="text-align: center;padding-top: 23px;">
                                <h5 class="modal-title text-center" id="paymentLabel">ទឹកប្រាក់ដែលបានបង់</h5>
                                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button> --}}
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive" style="padding: 0 10px">
                                    <table class="table table-striped">
                                        <thead class="">
                                        <th class="text-center" >ល.រ</th>
                                        <th class="text-nowrap">ឯកសារ</th>
                                        <th class="text-nowrap">ទឹកប្រាក់បានបង់($)</th>
                                        <th class="text-nowrap">ចំនួនទឹកប្រាក់នៅសល់($) </th>
                                        <th class="text-nowrap">ថ្ងៃបង់ប្រាក់</th>
                                        <th class="text-nowrap">បរិយាយ</th>
                                        <th class="text-nowrap">បង្កើតដោយ </th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button"  class="btn btn-primary printdiv-btn"  data-dismiss="modal" target="_blank">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- show history --}}
                <div class="modal fade" id="showhistory" tabindex="-1" role="dialog" aria-labelledby="paymentLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content printableArea">
                            <div class="" style="text-align: center;padding-top: 23px;">
                                <h5 class="modal-title text-center" id="paymentLabel">ប្រវត្តិកិច្ចសន្យា</h5>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive" style="padding: 0 10px">
                                    <table class="table table-striped">
                                        <thead class="">
                                            <th class="text-nowrap">ម្ចាស់ទ្រព្យសម្បត្តិ</th>
                                            <th class="text-nowrap">ទ្រព្យសម្បត្តិ</th>
                                            <th class="text-nowrap ">ឯកសារ</th>
                                            <th class="text-nowrap">ប្រភេទកុងត្រា</th>
                                            <th class="text-nowrap">ចំនួនទឹកប្រាក់($)</th>
                                            <th class="text-nowrap">ថ្ងៃចាប់ផ្ដើមអនុវត្ត </th>
                                            <th class="text-nowrap">ថ្ងៃផុតកំណត់ </th>
                                            <th class="text-nowrap">បង្កើតដោយ </th>
                                            <th class="text-nowrap">សកម្មភាព</th>
                                          </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button"  class="btn btn-primary printdiv-btn"  data-dismiss="modal" target="_blank">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript">
    function showData(data = null) {
        $('select[name="properties_id"]').val('');
        $('select[name="contract_type"]').val('');
        $('input[name="effective_date"]').val('');
        $('input[name="due_date"]').val('');
        $('input[name="full_amount"]').val('');
        $('input[name="remaining_amount"]').val('');
        $('textarea[name="description"]').val('');
        $('input[name="id"]').val('');
        $(".myselect2").select2();
        if(data){
            var obj = JSON.parse(data.data);

            $(".myselect2").val(data.property_id).trigger('change');
            $('select[name="properties_id"]').val(data.property_id);
            $('select[name="contract_type"]').val(obj.contract_type);
            $('input[name="effective_date"]').val(obj.effective_date);
            $('input[name="due_date"]').val(obj.due_date);
            $('input[name="full_amount"]').val(obj.full_amount);
            $('input[name="remaining_amount"]').val(obj.remaining_amount);
            $('textarea[name="description"]').val(obj.description);
            $('input[name="id"]').val(data.id);
        }
        $("#exampleModal").modal("show");

    }
    function payment(data){
        $('textarea[name="description"]').val('');
        $('input[name="id"]').val(data.id);
        var obj = JSON.parse(data.data);
            $('input[name="paid_date"]').val('');
            $('input[name="full_amount"]').val(obj.remaining_amount);
            $('input[name="remaining_amount"]').val(obj.remaining_amount);
        $('#payment').modal("show");

    }
    function showpayment(id){
        $.ajax({
            type:'get',
            url:"{{ route('contract.show') }}",
            data: {id:id},
            success:function(data){
                $('#showpayment table tbody').html(data);
            }
        });
      $('#showpayment').modal("show");
    }
    $(document).ready(function () {


        $("input[name='paid_amount']").keyup(function (e) {
            e.preventDefault();
            var input = parseFloat($(this).val());
            var original_am = $('input[name="full_amount"]').val();
            if( input > parseFloat(original_am)){
                $('input[name="paid_amount"]').val(0);
                input = 0;
            }
            var reman_amount = parseFloat(original_am) - input;
            $('input[name="remaining_amount"]').val(reman_amount);

        }).trigger('change');



    });
    $(document).on('click', '.printdiv-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var originalContent = $('body').html();
        var printArea = $this.parents('.printableArea').html();
        $('body').html(printArea);
        $('.close').remove();
        $('.modal-footer').remove();
        window.print();
        $('body').html(originalContent);
        $('.modal-backdrop').remove();
        location.reload();
    });
    function showhistory(id){
        $.ajax({
            type:'get',
            url:"{{ route('contract.history.show') }}",
            data: {id:id},
            success:function(data){
                $('#showhistory table tbody').html(data);
            }
        });
        $('#showhistory').modal("show");
    }


</script>

