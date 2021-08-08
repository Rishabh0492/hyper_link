@extends('admin.layouts.app')
@section('title')
@endsection

@section('css')
<link rel="stylesheet" href="{{URL::asset('resources/assets/admin/plugins/daterangepicker/daterangepicker.css')}}">
@endsection
@section('content')

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <small>Admin Dashboard</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">Dashboard</li>
      </ol>
	</section>

<section class="content">
	<div class="row">
            <div class="col-xs-12">
                <!-- Date and time range -->
                                        <div class="form-group pull-right">
                                          <!-- <label>Choose date</label> -->

                                          <div class="input-group">
                                            <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                                              <span>
                                                <i class="fa fa-calendar"></i> Select date
                                              </span>
                                              <i class="fa fa-caret-down"></i>
                                            </button>
                                          </div>
                                        </div>
                                        <!-- /.form group -->
            </div>
            <!-- /.col -->
    </div>
    <!-- Main content -->
    <div class="dashboardData">
        <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                    <div class="inner">
                        <h3 id="usercount">{{$totalUser}}</h3>
                        <p>Users</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-people-outline"></i>
                    </div>
                    <a href="{{url('admin/users')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
    </div>

    <!-- /.content -->
</section>
</div>

@endsection
@section('script')
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/jquery.validate.js')}}"></script>
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/additional-methods.js')}}"></script>
<script src="{{URL::asset('resources/assets/admin/plugins/daterangepicker/moment.js')}}"></script>
<script src="{{URL::asset('resources/assets/admin/plugins/daterangepicker/daterangepicker.js')}}"></script>
@if(Session::has('message'))
    <script>
        $(function() {
            toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
        });
    </script>
@endif
<script>
    $(document).ajaxStart(function() {
        Pace.restart();
    });
</script>

<script>
var SITE_URL = '<?php echo URL::to('/').'/'; ?>';

//Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment(),
        maxDate: new Date()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
                var start_date = start.format('MMMM D, YYYY');
                var end_date = end.format('MMMM D, YYYY')       
                  $.ajax({
                  url: SITE_URL + 'admin/dashboardFilterData',
                  type: 'POST',
                  dataType: 'html',
                  data: {"_token": "{{ csrf_token() }}",start_date: start_date,end_date: end_date},
                  success: function (data) {
                      $("#usercount").html(data);
                  }
              });
      }
    )
</script>
@endsection
  