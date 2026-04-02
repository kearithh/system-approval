<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
    	@if(basename(Request::url())=='toapprove')
  		<strong>To Approve</strong>
      @elseif(basename(Request::url())=='reject')
      	<strong>Rejected/Commented List</strong>
      @elseif(basename(Request::url())=='approved')
      	<strong>Approved List</strong>
      @elseif(basename(Request::url())=='pending')
        <strong>Pending List</strong>
      @endif
    </li>
    <li class="breadcrumb-item">
      <a href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company']}}">{{ @$_GET['company'] }}</a>
    </li>
      @if (@$_GET['type'])
          <li class="breadcrumb-item">
              <a href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'].'&type='.@$_GET['type'] }}">{{ strtoupper(@$_GET['type']) }}</a>
          </li>
      @endif
      @if (@$_GET['department'])
          <li class="breadcrumb-item">
              <a href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'].'&type='.@$_GET['type'].'&department='.@$_GET['department'] }}">{{ @$_GET['department'] }}</a>
          </li>
      @endif
      @if (@$_GET['tags'])
          <li class="breadcrumb-item">
              <a href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company'].'&type='.@$_GET['type'].'&department='.@$_GET['department'].'&tags='.@$_GET['tags'] }}">{{ strtoupper(@$_GET['tags']) }}</a>
          </li>
      @endif
  </ol>
</nav>
