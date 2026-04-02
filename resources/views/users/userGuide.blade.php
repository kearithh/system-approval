@extends('adminlte::page', ['activePage' => 'user-management', 'titlePage' => __('User Management')])
@section('plugins.Select2', true)
@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">


            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('របៀបប្រើប្រាស់ប្រព័ន្ធ') }}</h4>
              </div>
              <div class="card-body ">
                  <ul>
                      <li>
                          <a href="{{ asset('lesson/E-Approval_User_Guide_V5.pdf') }}">កាណែនាំអំពីរបៀបប្រើប្រាស់ប្រព័ន្ធ E-Approval</a>
                      </li>
                      <li>
                          <a href="{{ asset('lesson/Loan_Request_E-Approval_User_Guide_V3.pdf') }}">អំពីការប្រើប្រាស់ សំណើរសុំអនុម័តឥណទាន</a>
                      </li>
                      <li>
                          <a href="{{ asset('lesson/Loan_Adjustment_E-Approval_User_Guide.pdf') }}">អំពីការប្រើប្រាស់ សំណើរសុំកែប្រែតារាងកាលវិភាគសងប្រាក់អតិថិជន</a>
                      </li>
                      <li>
                          <a href="{{ asset('lesson/E-Approval_User_Guide_Penalty&Cutting_Interest_Final.pdf') }}">អំពីការប្រើប្រាស់ សំណើសុំកាត់ប្រាក់ពិន័យ (Form ថ្មី) និងសំណើសុំកាត់ការប្រាក់អតិថិជន </a>
                      </li>
{{--                      <li>--}}
{{--                          អំពីការប្រើប្រាស់ Menu Report--}}
{{--                      </li>--}}
                  </ul>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
