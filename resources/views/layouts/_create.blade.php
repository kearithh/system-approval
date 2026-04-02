@if (auth()->id() != 11)
    <li class=" nav-item has-treeview  @if(request()->segment(2) == 'create') menu-open @endif">
        <a class="nav-link nav-item @if(request()->segment(2) == 'create') active @endif" href="#">
            <i class="fas fa-fw fa-paper-plane "></i>
            <p>
                Create Request
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_memo') active @endif" href="/request_memo/create">
                    <i class="fas fa-plus"></i>
                    <p>Memo</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'custom_letter') active @endif" href="/custom_letter/create">
                    <i class="fas fa-plus "></i>
                    <p>Custom Letter</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'hr_request') active @endif" href="/hr_request/create">
                    <i class="fas fa-plus "></i>
                    <p>Letter</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'resign') active @endif" href="/resign/create">
                    <i class="fas fa-plus "></i>
                    <p>Resign Letter</p>
                </a>
            </li>
            
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request') active @endif" href="/request/create">
                    <i class="fas fa-plus "></i>
                    <p>Special Expense</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_grn') active @endif" href="/request_grn/create">
                    <i class="fas fa-plus "></i>
                    <p>GRN</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_po') active @endif" href="/request_po/create">
                    <i class="fas fa-plus "></i>
                    <p>PO Request</p>
                <a class="nav-link @if(request()->segment(1) == 'request_pr') active @endif" href="/request_pr/create">
                    <i class="fas fa-plus "></i>
                    <p>PR Request</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_hr') active @endif" href="/request_hr/create">
                    <i class="fas fa-plus "></i>
                    <p>General Expense</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'damagedlog') active @endif" href="/damagedlog/create">
                    <i class="fas fa-plus "></i>
                    <p>Damaged Asset</p>
                </a>
	        </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'disposal') active @endif" href="/disposal/create">
                    <i class="fas fa-plus "></i>
                    <p>Disposal Asset</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'transfer_asset') active @endif" href="/transfer_asset/create">
                    <i class="fas fa-plus "></i>
                    <p>Transfer Asset</p>
                </a>
            </li>
        
            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'sale_asset') active @endif" href="/sale_asset/create">
                    <i class="fas fa-plus "></i>
                    <p>Sale-Asset</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'cash_advance') active @endif" href="/cash_advance/create">
                    <i class="fas fa-plus "></i>
                    <p>Cash Advance</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_ot') active @endif" href="/request_ot/create">
                    <i class="fas fa-plus "></i>
                    <p>Request OT</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'training') active @endif" href="/training/create">
                    <i class="fas fa-plus "></i>
                    <p>Training</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_create_user') active @endif" href="/request_create_user/create">
                    <i class="fas fa-plus "></i>
                    <p>Request User</p>
                </a>
            </li>

            <li class=" nav-item ">
                <a class="nav-link @if(request()->segment(1) == 'request_disable_user') active @endif" href="/request_disable_user/create">
                    <i class="fas fa-plus "></i>
                    <p>Request Disable User</p>
                </a>
            </li>

            @if (@auth()->user()->branch->branch == 0)
                <li class=" nav-item ">
                    <a class="nav-link @if(request()->segment(1) == 'group_request') active @endif" href="/group_request/create">
                        <i class="fas fa-plus "></i>
                        <p>Report</p>
                    </a>
                </li>

                <li class=" nav-item ">
                    <a class="nav-link @if(request()->segment(1) == 'policy') active @endif" href="/policy/create">
                        <i class="fas fa-plus "></i>
                        <p>Policy / SOP</p>
                    </a>
                </li>

                <li class=" nav-item ">
                    <a class="nav-link @if(request()->segment(1) == 'setting-reviewer-approver') active @endif" href="/setting-reviewer-approver/create">
                        <i class="fas fa-plus "></i>
                        <p>Setting Auto Approver</p>
                    </a>
                </li>
            @endif

        </ul>
    </li>
@endif
