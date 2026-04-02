    @if (!isset($item['topnav']) || (isset($item['topnav']) && !$item['topnav']))
        @if (is_string($item))
            <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-header">{{ $item }}</li>
        @elseif (isset($item['header']))
            <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-header">{{ $item['header'] }}</li>
        @elseif (isset($item['search']) && $item['search'])
            <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif>
                <form action="{{ $item['href'] }}" method="{{ $item['method'] }}" class="form-inline">
                    <div class="input-group">
                        <input class="form-control form-control-sidebar" type="search" name="{{ $item['input_name'] }}" placeholder="{{ $item['text'] }}" aria-label="{{ $item['aria-label'] ?? $item['text'] }}">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </li>
        @else
            @if (Auth::user()->position->level == config('app.position_level_president'))
                @if (!($item['text'] == 'Create Request' || $item['text'] == 'Pending List' || @$item['name'] == 'open_list' || @$item['name'] == 'open_approved' || @$item['name'] == 'open_rejected' || $item['text'] == 'Setting'))
                    <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="@if(@$item['name'] == 'open' || @$item['text'] == 'To Approve List' || @$item['text'] == 'Rejected/Commented List' || @$item['text'] == 'Approved List') menu-open @endif nav-item @if (isset($item['submenu'])){{ $item['submenu_class'] }}@endif">
                        <a class="nav-link {{ $item['class'] }}" href="{{ $item['href'] }} "

                           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
                        >
                            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                            <p>
                                {{ $item['text'] }}

                                @if (isset($item['submenu']))
                                    <i class="fas fa-angle-left right"></i>
                                @endif

                                <!------To Approve------------------------------------------------------------------------------------>
                                @if (@$item['name'] == 'stsk_approval' && @$viewShare['president_approval']['STSK'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_approval' && @$viewShare['president_approval']['MFI'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_approval' && @$viewShare['president_approval']['NGO'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_approval' && @$viewShare['president_approval']['ORD'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_approval' && @$viewShare['president_approval']['ST'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_approval' && @$viewShare['president_approval']['MMI'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_approval' && @$viewShare['president_approval']['MHT'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_approval' && @$viewShare['president_approval']['TSP'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['TSP'] }}
                                    </span>

                                <!------Reject------------------------------------------------------------------------------------>
                                @elseif(@$item['name'] == 'stsk_rejected' && @$viewShare['president_rejected']['STSK'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_rejected' && @$viewShare['president_rejected']['MFI'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_rejected' && @$viewShare['president_rejected']['NGO'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_rejected' && @$viewShare['president_rejected']['ORD'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_rejected' && @$viewShare['president_rejected']['ST'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_rejected' && @$viewShare['president_rejected']['MMI'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_rejected' && @$viewShare['president_rejected']['MHT'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_rejected' && @$viewShare['president_rejected']['TSP'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['TSP'] }}
                                    </span>

                                <!------Approved------------------------------------------------------------------------------------>
                                @elseif (@$item['name'] == 'stsk_approved' && @$viewShare['president_approved']['STSK'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_approved' && @$viewShare['president_approved']['MFI'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_approved' && @$viewShare['president_approved']['NGO'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_approved' && @$viewShare['president_approved']['ORD'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_approved' && @$viewShare['president_approved']['ST'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_approved' && @$viewShare['president_approved']['MMI'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_approved' && @$viewShare['president_approved']['MHT'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_approved' && @$viewShare['president_approved']['TSP'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['TSP'] }}
                                    </span>
                                @endif
                            </p>
                        </a>
                        @if (isset($item['submenu']))
                            <ul class="nav nav-treeview">
                                @each('adminlte::partials.menu-item', $item['submenu'], 'item')
                            </ul>
                        @endif
                    </li>
                @endif
            @elseif (auth()->user()->username != 'admin' || auth()->user()->role != 1)
                @if (!($item['text'] == 'Setting'))
                    <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="@if(basename(Request::url())== @$item['status'] ) menu-open @endif nav-item @if (isset($item['submenu'])){{ $item['submenu_class'] }}@endif">
                        <a

                            class="nav-link {{ $item['class'] }} @if($item['text'] == @$_GET['company']) active @endif"
                            href="{{ $item['href'] }}"

                           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
                        >
                            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                            <p>
                                {{ $item['text'] }}

                                @if (isset($item['submenu']))
                                    <i class="fas fa-angle-left right"></i>
                                @endif
                            </p>
                        </a>
                        @if (isset($item['submenu']))
                            <ul class="nav nav-treeview">
                                @each('adminlte::partials.menu-item', $item['submenu'], 'item')
                            </ul>
                        @endif
                    </li>
                @endif
            @else
{{--                @if (!($item['text'] == 'Create Request' || $item['text'] == 'Pending List' || @$item['name'] == 'open_list' || @$item['name'] == 'open_approved' || @$item['name'] == 'open_rejected'))--}}
                    <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="@if(basename(Request::url())== @$item['status'] ) menu-open @endif nav-item @if (isset($item['submenu'])){{ $item['submenu_class'] }}@endif">
                        <a

                            class="nav-link {{ $item['class'] }} @if($item['text'] == @$_GET['company']) active @endif"
                            href="{{ $item['href'] }}"

                           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
                        >
                            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                            <p>
                                {{ $item['text'] }}

                                @if (isset($item['submenu']))
                                    <i class="fas fa-angle-left right"></i>
                                @endif

                            <!------To Approve------------------------------------------------------------------------------------>
                                @if (@$item['name'] == 'stsk_approval' && @$viewShare['president_approval']['STSK'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_approval' && @$viewShare['president_approval']['MFI'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_approval' && @$viewShare['president_approval']['NGO'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_approval' && @$viewShare['president_approval']['ORD'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_approval' && @$viewShare['president_approval']['ST'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_approval' && @$viewShare['president_approval']['MMI'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_approval' && @$viewShare['president_approval']['MHT'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_approval' && @$viewShare['president_approval']['TSP'] > 0)
                                    <span class="badge badge-info right">
                                        {{ @$viewShare['president_approval']['TSP'] }}
                                    </span>

                                    <!------Reject------------------------------------------------------------------------------------>
                                @elseif(@$item['name'] == 'stsk_rejected' && @$viewShare['president_rejected']['STSK'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_rejected' && @$viewShare['president_rejected']['MFI'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_rejected' && @$viewShare['president_rejected']['NGO'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_rejected' && @$viewShare['president_rejected']['ORD'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_rejected' && @$viewShare['president_rejected']['ST'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_rejected' && @$viewShare['president_rejected']['MMI'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_rejected' && @$viewShare['president_rejected']['MHT'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_rejected' && @$viewShare['president_rejected']['TSP'] > 0)
                                    <span class="badge badge-danger right">
                                        {{ @$viewShare['president_rejected']['TSP'] }}
                                    </span>


                                <!------Pending------------------------------------------------------------------------------------>
                                @elseif(@$item['name'] == 'stsk_pending' && @$viewShare['president_pending']['STSK'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_pending' && @$viewShare['president_pending']['MFI'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_pending' && @$viewShare['president_pending']['NGO'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_pending' && @$viewShare['president_pending']['ORD'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_pending' && @$viewShare['president_pending']['ST'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_pending' && @$viewShare['president_pending']['MMI'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_pending' && @$viewShare['president_pending']['MHT'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_pending' && @$viewShare['president_pending']['TSP'] > 0)
                                    <span class="badge badge-warning right">
                                        {{ @$viewShare['president_pending']['TSP'] }}
                                    </span>

                                    <!------Approved--------------------------------------------------------------------------------->
                                @elseif (@$item['name'] == 'stsk_approved' && @$viewShare['president_approved']['STSK'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['STSK'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mfi_approved' && @$viewShare['president_approved']['MFI'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MFI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ngo_approved' && @$viewShare['president_approved']['NGO'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['NGO'] }}
                                    </span>
                                @elseif(@$item['name'] == 'ord_approved' && @$viewShare['president_approved']['ORD'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['ORD'] }}
                                    </span>
                                @elseif(@$item['name'] == 'st_approved' && @$viewShare['president_approved']['ST'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['ST'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mmi_approved' && @$viewShare['president_approved']['MMI'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MMI'] }}
                                    </span>
                                @elseif(@$item['name'] == 'mht_approved' && @$viewShare['president_approved']['MHT'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['MHT'] }}
                                    </span>
                                @elseif(@$item['name'] == 'tsp_approved' && @$viewShare['president_approved']['TSP'] > 0)
                                    <span class="badge badge-success right">
                                        {{ @$viewShare['president_approved']['TSP'] }}
                                    </span>
                                @endif
                            </p>
                        </a>
                        @if (isset($item['submenu']))
                            <ul class="nav nav-treeview">
                                @each('adminlte::partials.menu-item', $item['submenu'], 'item')
                            </ul>
                        @endif
                    </li>
{{--                @endif--}}

{{--                @if (!(@$item['name'] == 'open_list1' || @$item['name'] == 'open_approved1' || @$item['name'] == 'open_rejected1'))--}}
{{--                    <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="@if(@$item['name'] == 'open' || @$item['text'] == 'To Approve List' || @$item['text'] == 'Rejected/Commented List') menu-open @endif nav-item @if (isset($item['submenu'])){{ $item['submenu_class'] }}@endif">--}}
{{--                        <a class="nav-link {{ $item['class'] }}" href="{{ $item['href'] }} "--}}
{{--                           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif--}}
{{--                        >--}}
{{--                            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>--}}
{{--                            <p>--}}
{{--                                {{ $item['text'] }}--}}

{{--                                @if (isset($item['submenu']))--}}
{{--                                    <i class="fas fa-angle-left right"></i>--}}
{{--                                @endif--}}

{{--                                <!------To Approve------------------------------------------------------------------------------------>--}}
{{--                                @if (@$item['name'] == 'memo_approval' && $viewShare['memo_approval'] > 0)--}}
{{--                                    <span class="badge badge-info right">--}}
{{--                                        {{ @$viewShare['memo_approval'] }}--}}
{{--                                    </span>--}}
{{--                                @elseif(@$item['name'] == 'se_approval' && $viewShare['se_approval'] > 0)--}}
{{--                                    <span class="badge badge-info right">--}}
{{--                                        {{ @$viewShare['se_approval'] }}--}}
{{--                                    </span>--}}
{{--                                @elseif(@$item['name'] == 'ge_approval' && $viewShare['ge_approval'] > 0)--}}
{{--                                    <span class="badge badge-info right">--}}
{{--                                        {{ @$viewShare['ge_approval'] }}--}}
{{--                                    </span>--}}
{{--                                @elseif(@$item['name'] == 'disposal_approval' && $viewShare['disposal_approval'] > 0)--}}
{{--                                    <span class="badge badge-info right">--}}
{{--                                        {{ @$viewShare['disposal_approval'] }}--}}
{{--                                    </span>--}}
{{--                                @elseif(@$item['name'] == 'damagedlog_approval' && $viewShare['damagedlog_approval'] > 0)--}}
{{--                                    <span class="badge badge-info right">--}}
{{--                                        {{ @$viewShare['damagedlog_approval'] }}--}}
{{--                                    </span>--}}

{{--                                <!------Reject------------------------------------------------------------------------------------>--}}
{{--                                @elseif(@$item['name'] == 'reject_memo_approval' && $viewShare['reject_memo_approval'] > 0)--}}
{{--                                    <span class="badge badge-danger right">--}}
{{--                                        {{ @$viewShare['reject_memo_approval'] }}--}}
{{--                                    </span>--}}

{{--                                @elseif(@$item['name'] == 'reject_se_approval' && $viewShare['reject_se_approval'] > 0)--}}
{{--                                    <span class="badge badge-danger right">--}}
{{--                                        {{ @$viewShare['reject_se_approval'] }}--}}
{{--                                    </span>--}}

{{--                                @elseif(@$item['name'] == 'reject_ge_approval' && $viewShare['reject_ge_approval'] > 0)--}}
{{--                                    <span class="badge badge-danger right">--}}
{{--                                        {{ @$viewShare['reject_ge_approval'] }}--}}
{{--                                    </span>--}}

{{--                                @elseif(@$item['name'] == 'reject_disposal_approval' && $viewShare['reject_disposal_approval'] > 0)--}}
{{--                                    <span class="badge badge-danger right">--}}
{{--                                        {{ @$viewShare['reject_disposal_approval'] }}--}}
{{--                                    </span>--}}

{{--                                @elseif(@$item['name'] == 'reject_damagedlog_approval' && $viewShare['reject_damagedlog_approval'] > 0)--}}
{{--                                    <span class="badge badge-danger right">--}}
{{--                                        {{ @$viewShare['reject_damagedlog_approval'] }}--}}
{{--                                    </span>--}}
{{--                                    --}}
{{--                                @endif--}}
{{--                            </p>--}}
{{--                        </a>--}}
{{--                        @if (isset($item['submenu']))--}}
{{--                            <ul class="nav nav-treeview">--}}
{{--                                @each('adminlte::partials.menu-item', $item['submenu'], 'item')--}}
{{--                            </ul>--}}
{{--                        @endif--}}
{{--                    </li>--}}
{{--                @endif--}}
            @endif
        @endif
    @endif
