<?php
$menu = @$_GET['menu'];
$type = @$_GET['type'];
$key = $menu.'_'.$type.'_next_pre';
?>
@if (session(@$key) && in_array(@$_GET['menu'], ['approved', 'toapprove', 'to_approve_report']) && in_array(auth()->id(), [11, 39, 54]))
    <a class="btn btn-default btn-sm @if (!preBtn()) disabled @endif" title="Previous Page"  href="{{ preBtn() }}">Previous</a>
    <a class="btn btn-default btn-sm @if (!nextBtn()) disabled @endif" title="Next Page" href="{{ nextBtn() }}" style="margin-right: 20px">Next</a>
@endif


