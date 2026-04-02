{{--<div class="modal fade" id="bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="exampleModalLongTitle">Create Item</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <form--}}
{{--                        action="{{ action('RequestItemController@store') }}"--}}
{{--                        method="POST"--}}
{{--                        enctype="multipart/form-data"--}}
{{--                >--}}
{{--                    @csrf--}}

{{--                    <input type="hidden" class="request_token" name="request_token" value="{{ encrypt($requestForm->id) }}">--}}
{{--                    <input type="hidden" class="request_token" name="request_id" value="{{ $requestForm->id }}">--}}
{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('ឈ្មោះ') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"--}}
{{--                                        name="name"--}}
{{--                                        id="input-name"--}}
{{--                                        type="text"--}}
{{--                                        placeholder="{{ __('Name') }}"--}}
{{--                                        value="{{ old('name') }}"--}}
{{--                                        required="true"--}}
{{--                                        aria-required="true"--}}
{{--                                />--}}
{{--                                @if ($errors->has('name'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('បរិយាយ') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('desc') ? ' is-invalid' : '' }}"--}}
{{--                                        name="desc"--}}
{{--                                        id="input-name"--}}
{{--                                        type="text"--}}
{{--                                        placeholder="{{ __('Description') }}"--}}
{{--                                        value="{{ old('desc') }}"--}}
{{--                                        required="true"--}}
{{--                                        aria-required="true"--}}
{{--                                />--}}
{{--                                @if ($errors->has('desc'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('desc') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('បរិមាណ') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('qty') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('qty') ? ' is-invalid' : '' }}"--}}
{{--                                        name="qty"--}}
{{--                                        id="qty"--}}
{{--                                        type="number"--}}
{{--                                        placeholder="{{ __('QTY') }}"--}}
{{--                                        value="{{ old('qty') }}"--}}
{{--                                        required="true"--}}
{{--                                        aria-required="true"--}}
{{--                                />--}}
{{--                                @if ($errors->has('qty'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('qty') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('តម្លៃរាយ($)') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('unit_price') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"--}}
{{--                                        name="unit_price"--}}
{{--                                        id="unit_price"--}}
{{--                                        type="number"--}}
{{--                                        placeholder="{{ __('Unit price') }}"--}}
{{--                                        value="{{ old('unit_price') }}"--}}
{{--                                        required="true"--}}
{{--                                        aria-required="true"--}}
{{--                                />--}}
{{--                                @if ($errors->has('unit_price'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('unit_price') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('ពន្ធអាករ(%)') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('vat') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('vat') ? ' is-invalid' : '' }}"--}}
{{--                                        name="vat"--}}
{{--                                        id="vat"--}}
{{--                                        type="number"--}}
{{--                                        placeholder="{{ __('VAT') }}"--}}
{{--                                        value="{{ old('vat', 0) }}"--}}
{{--                                />--}}
{{--                                @if ($errors->has('unit_price'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('unit_price') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('ផ្សេងៗ') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group{{ $errors->has('desc') ? ' has-danger' : '' }}">--}}
{{--                                <input--}}
{{--                                        class="form-control{{ $errors->has('remark') ? ' is-invalid' : '' }}"--}}
{{--                                        name="remark"--}}
{{--                                        id="remark"--}}
{{--                                        type="text"--}}
{{--                                        placeholder="{{ __('Remark') }}"--}}
{{--                                        value="{{ old('remark') }}"--}}
{{--                                />--}}
{{--                                @if ($errors->has('remark'))--}}
{{--                                    <span id="name-error" class="error text-danger" for="remark">{{ $errors->first('remark') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label">{{ __('សរុប($)') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group">--}}
{{--                                <input--}}
{{--                                        id="amount"--}}
{{--                                        class="form-control"--}}
{{--                                        type="text"--}}
{{--                                        value="$ 0.00"--}}
{{--                                        disabled--}}
{{--                                />--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}



{{--                    <div class="row">--}}
{{--                        <label class="col-sm-3 col-form-label" for="input-password-confirmation">{{ __('Quote') }}</label>--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <div class="form-group form-file-upload">--}}
{{--                                <input name="quote[]" type="file" multiple="" class="" style="z-index: 1; opacity: 1; height: 28px">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class="modal-footer">--}}
{{--                        <button type="submit" class="btn btn-success">Submit</button>--}}
{{--                        <button type="button" value="0" name="submit" class="btn btn-secondary" data-dismiss="modal">Cancel</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
