<?php
    $setting = [
        'properties-owner',
        'properties',
        'contract'
    ];
    $allowedUsers = [28,2261,2275,2305,63,2112,803,1810,2844,24];

?>
<!-- admin, 28 = linkim  -->
@if (@admin_action() || in_array(auth()->id(),$allowedUsers))
    <li class="nav-item has-treeview @if(in_array(request()->segment(1), $setting)) menu-open @endif">
        <a class="nav-link nav-item @if(in_array(request()->segment(1), $setting)) active @endif" href="#">
            <i class="fas fa-fw fa-sliders-h "></i>
            <p>
                Contract Managements
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'properties-owner') active @endif" href="properties-owner">
                    <i class="fas fa-fw fa-user "></i>
                    <p>
                        Properties Owner
                    </p>
                </a>
            </li>
        </ul>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'properties') active @endif" href="properties">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    <p>
                        Properties
                    </p>
                </a>
            </li>
        </ul>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'contract') active @endif" href="contract">
                    <i class="fas fa-fw fa-edit " aria-hidden="true"></i>
                    <p>
                        Contract
                    </p>
                </a>
            </li>
        </ul>
    </li>
@endif
