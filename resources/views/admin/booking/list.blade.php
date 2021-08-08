@extends('admin.layouts.app')
@section('title') Booking |@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Booking</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{url('admin/roles')}}"><i class="fa fa-dashboard"></i> Booking</a></li>
            <li class="active">All Booking</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-2 pull-right" style="padding-bottom: 10px;">
                <a href="{{route('create-booking')}}"><button type="button" class="btn btn-block btn-primary">New Booking</button></a>
            </div>

            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
<table class='table table-bordered'>
  <tr>
    <th>Airline</th>
    <th>User Name</th>
    <th>travel date</th>
  </tr>
  @if(!empty($booking))
  @foreach($booking as $key=>$value)
  <tr>
    <td>{{$value->airline->name}}</td>
    <td>{{$value->user->first_name.' '.$value->user->last_name}}</td>
    <td>{{date('Y-m-d',strtotime($value->travell_date))}}</td>
  </tr>
  @endforeach
  @endif
</table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
@endsection

