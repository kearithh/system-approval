<?php
    $setting = [
        'user',
        'company',
        'position',
        'branch',
        'department',
        'setting_memo',
        'setting_group_support',
        'benefit_ot',
        'approve_report',
        'approve_request',
        'setting-approver-report',
    ];
?>

<!-- admin, 28 = linkim  -->
@if (@admin_action() || auth()->id() == 28) 
    <li class="nav-item has-treeview @if(in_array(request()->segment(1), $setting)) menu-open @endif">
        <a class="nav-link nav-item @if(in_array(request()->segment(1), $setting)) active @endif" href="#">
            <i class="fas fa-fw fa-sliders-h "></i>
            <p>
                Setting
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'user') active @endif" href="/user">
                    <i class="fas fa-fw fa-user "></i>
                    <p>
                        Staff
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'company') active @endif" href="/company">
                    <i class="fas fa-building "></i>
                    <p>
                        Company
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'position') active @endif" href="/position">
                    <i class="fas fa-fw fa-crosshairs "></i>
                    <p>
                        Position
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'branch') active @endif" href="/branch">
                    <i class="fas fa-code-branch "></i>
                    <p>
                        Branch
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'department') active @endif" href="/department">
                    <i class="fas fa-building "></i>
                    <p>
                        Department
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'setting-approver-report') active @endif" href="/setting-approver-report">
                    <i class="fas fa-fw fa-cog"></i>
                    <p>
                        Setting Approver Report
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'setting_memo') active @endif" href="/setting_memo">
                    <i class="fas fa-fw fa-cog"></i>
                    <p>
                        Setting Memo
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'benefit_ot') active @endif" href="/benefit_ot">
                    <i class="fas fa-fw fa-cog"></i>
                    <p>
                        Benefit OT
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'setting_group_support') active @endif" href="/setting_group_support">
                    <i class="fas fa-fw fa-cog"></i>
                    <p>
                        Setting Group Support
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'approve_request') active @endif" href="/approve_request">
                    <i class="fas fa-fw fa-check"></i>
                    <p>
                        Approve Request
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'approve_report') active @endif" href="/approve_report">
                    <i class="fas fa-fw fa-check"></i>
                    <p>
                        Approve Report
                    </p>
                </a>
            </li>
        </ul>
    </li>
@endif