<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title"><strong>Type</strong></h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a
                    class="nav-link @if(@$_GET['type']=='Memo') text-primary @endif"
                    href="{{ URL::current().'?'.appendQueryString('type', 'Memo')}}">
                    <i class="fas fa-gavel"></i>
                    Memo
                    @if(@$memo > 0)
                        <span class="badge badge-info right">{{ @$memo }}</span
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link @if(@$_GET['type']=='Special') text-primary @endif"
                    href="{{ URL::current().'?'.appendQueryString('type', 'Special')}}">
                <i class="fas fa-star"></i>
                    Special
                    @if(@$memo > 0)
                        <span class="badge badge-info right">{{ @$memo }}</span
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link @if(@$_GET['type']=='General') text-primary @endif"
                    href="{{ URL::current().'?'.appendQueryString('type', 'General')}}">
                <i class="fas fa-money-check-alt"></i>
                    General
                    @if(@$memo > 0)
                        <span class="badge badge-info right">{{ @$memo }}</span
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link @if(@$_GET['type']=='Disposal') text-primary @endif"
                    href="{{ URL::current().'?'.appendQueryString('type', 'Disposal')}}">
                <i class="fas fa-minus-circle"></i>
                    Disposal
                    @if(@$memo > 0)
                        <span class="badge badge-info right">{{ @$memo }}</span
                    @endif
                </a>
            </li>

        </ul>
    </div>
</div>
