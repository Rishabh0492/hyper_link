@extends('admin.layouts.app')
@section('title')  User Details | @endsection
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>User Details</h1>
    <ol class="breadcrumb">
      <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{url('admin/users')}}"> Users</a> </li>
      <li class="active">User Details</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <!-- Horizontal Form -->
        <div class="box ">
          <!-- /.box-header -->
          <div class="col-sm-8">
                <div class="box-body">
                    <div class="table-responsive">
                                <table class="table table-user-information">
                                    <tbody>
                                        <tr>
                                            <td><strong>First Name</strong></td>
                                            <td class="text-primary">{{$user->first_name}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>Last Name</strong></td>
                                            <td class="text-primary">{{$user->last_name}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>Email</strong></td>
                                            <td class="text-primary">{{$user->email}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>Phone Number</strong></td>
                                            <td class="text-primary">{{$user->user_mobile}}</td>
                                        </tr>
                                       
                                         <tr>
                                            <td><strong>Date of Birth</strong></td>
                                            <td class="text-primary">{{$user->dob}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>Country</strong></td>
                                            <td class="text-primary">{{$user->countyData->name}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>State</strong></td>
                                            <td class="text-primary">{{$user->stateData->name}}</td>
                                        </tr>
                                         <tr>
                                            <td><strong>City</strong></td>
                                            <td class="text-primary">{{$user->cityData->name}}</td>
                                        </tr>
                                      </tbody>
                                </table>
                            </div>
                </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group" style="margin-top: 15px;">
                <div class="vc_column-inner ">
                  <label class="cabinet center-block">
                    <figure>
                      <img src="@if(isset($user))@if($user->getOriginal('profile_image')){{$user->profile_image}} @endif @else {{ URL::asset('/resources/assets/img/user.png')}} @endif " class="gambar old_profile_imageSub" id="item-img-output"  name="avatar" width="300px"/>
                    </figure>
                    <p>
                    </p>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
                <div class="" style="border-top:0">
                    <div class="box-footer">
                        <a type="button" href="{{url('/admin/users')}}" id="cancelBtn" class="btn btn-default pull-right">Back</a>
                    </div>
                </div>
            </div>
            <div class="clearfix">
            </div>
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </section>
    </div>
  @endsection
