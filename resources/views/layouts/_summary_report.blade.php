@if (@auth()->user()->branch->branch == 0)
    <li class="nav-item has-treeview @if(request()->segment(1) == 'summary_report') menu-open @endif">
        <a class="nav-link nav-item @if(request()->segment(1) == 'summary_report') active @endif" href="#">
            <i class="fas fa-fw fa-chart-bar "></i>
            <p>
                Summary Report
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'special-expense') active @endif" href="/summary_report/special-expense">
                    <i class="fas fa-star"></i>
                    <p>
                        Special Expense
                    </p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'pr-request') active @endif" href="/summary_report/pr-request">
                    <i class="fas fa-star"></i>
                    <p>
                        PR Request
                    </p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'po-request') active @endif" href="/summary_report/po-request">
                    <i class="fas fa-star"></i>
                    <p>
                        PO Request
                    </p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'grn') active @endif" href="/summary_report/grn">
                    <i class="fas fa-star"></i>
                    <p>
                        GRN
                    </p>
                </a>
            </li>
            
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'general-expense') active @endif" href="/summary_report/general-expense">
                    <i class="fas fa-money-check-alt"></i>
                    <p>
                        General Expense
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'ot-report') active @endif" href="/summary_report/ot-report">
                    <i class="fas fa-clipboard"></i>
                    <p>
                        OT
                    </p>
                </a>
            </li>
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'memo') active @endif" href="/summary_report/memo">
                    <i class="fas fa-file"></i>
                    <p>
                        Memo
                    </p>
                </a>
            </li>
            @if (@admin_action() || @hr_action())
                <li class=" nav-item ">
                    <a class="nav-link @if(request()->segment(2) == 'resign_letter') active @endif" href="/summary_report/resign_letter">
                        <i class="fas fa-envelope"></i>
                        <p>
                            Resign letter
                        </p>
                    </a>
                </li>
            @endif
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(2) == 'report') active @endif" href="/summary_report/report">
                    <i class="fas fa-file-invoice"></i>
                    <p>
                        Tracking Report
                    </p>
                </a>
            </li>
        </ul>
    </li>
@endif