<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      @if(basename(Request::url())=='toapprove')
        <strong>To Approve</strong>
      @elseif(basename(Request::url())=='reject')
        <strong>Commented List</strong>
      @elseif(basename(Request::url())=='disable')
        <strong>Rejected List</strong>
      @elseif(basename(Request::url())=='approved')
        <strong>Approved List</strong>
      @elseif(basename(Request::url())=='pending')
        <strong>Pending List</strong>
      @endif
    </li>
    <li class="breadcrumb-item">
      <a href="{{ url('/'.basename(Request::url()).'?company=').@$_GET['company']}}">{{ @$_GET['company'] }}</a>
    </li>
    <li class="breadcrumb-item">
      {{ @$_GET['type'] }}
      <!-- <li class="dropdown">
        <a class="dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Memo
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="">Special</a>
          <a class="dropdown-item" href="{{ url('/toapprove?company=').@$_GET['company'] . '&type=General'}}">General</a>
          <a class="dropdown-item" href="{{ url('/toapprove?company=').@$_GET['company'] . '&type=Disposal'}}">Disposal</a>
        </div>
      </li> -->
    </li>
  </ol>
</nav>