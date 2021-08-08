@extends('admin.layouts.app')

@section('title') {{$action}} Role User | @endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>{{$action}} Role User</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{url('admin/roleuser')}}"><i class="fa fa-dashboard"></i> Role User</a></li>
            <li class="active">{{$action}} Role User</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <!-- Horizontal Form -->
                <div class="box">
                    <!-- /.box-header -->
                    <!-- form start -->
                     @if(isset($roleuser))
                        {{ Form::model($roleuser, ['route' => ['roleuser.update', $roleuser->id], 'method' => 'patch','class' => 'form-horizontal','enctype'=>'multipart/form-data','id'=>'roleForm']) }}
                    @else
                        {{ Form::open(['route' => 'roleuser.store','class' => 'form-horizontal','enctype'=>'multipart/form-data','id'=>'roleForm']) }}
                    @endif
                    <div class="col-sm-12">
                            <div class="box-body">

                                <div class="form-group">
                                  <label  class=" control-label" for="geo_hub_name">Select Role <span class="colorRed"> *</span></label>
                                  <div class=""> 
                                    {{-- !!Form::select('name', $roles->pluck('name','id'),null, ['class' => 'form-control','id'=>'name'])!! --}}
                                    <select name="name" id="name" class="form-control">
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        @if(isset($role_id) && $role->id == $role_id) selected @endif
                                        >{{ $role->name }}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('name'))
                                    <span class="help-block alert alert-danger">
                                      <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                  </div>
                                </div>
                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                               <label  class=" control-label" for="first_name">Full Name <span class="colorRed"> *</span></label>
                                <div class="row">
                                <div class="col-sm-6 jointbox">
                                    {{ Form::text('first_name', Request::old('first_name'),['class'=>'form-control','placeholder'=>"First Name"]) }}
                                    @if ($errors->has('first_name'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                    <div class="col-sm-6 ">
                                        {{ Form::text('last_name', Request::old('last_name'),['class'=>'form-control','placeholder'=>"Last Name"]) }}
                                        @if ($errors->has('last_name'))
                                            <span class="help-block alert alert-danger">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                              </div>
                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label  class=" control-label" for="email">Email <span class="colorRed"> *</span></label>
                                    <div class="">
                                       {{ Form::text('email', Input::old('email'),['class'=>'form-control','placeholder'=>"Email"]) }}
                                        @if ($errors->has('email'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-sm-6 ">
                                <p>Please select your Gender:</p>
                                    <input type="radio"name="gender" value="30">
                                    <label for="gender">Male</label><br>
                                    <input type="radio" name="gender" value="f">
                                    <label for="gender">Female</label><br>  
                                </div>  
                                <div class="col-sm-6 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                    <label  class=" control-label" for="dob">DOB <span class="colorRed"> *</span></label>
                                    <div class="">
                                       {{ Form::date('dob', Input::old('dob'),['class'=>'form-control','placeholder'=>"DOB"]) }}
                                        @if ($errors->has('dob'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('dob') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                </div>
                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label  class=" control-label" for="password">Password <span class="colorRed"> *</span></label>
                                    <div class="row">
                                        <div class="col-sm-6 jointbox">
                                            <input autocomplete="new-password" type="password" class="form-control" id="password" name="password" placeholder="Password" value="{{old('password')}}"/>
                                            @if ($errors->has('password'))
                                            <span class="help-block alert alert-danger">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 ">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" value="{{old('confirm_password')}}"/>
                                            @if ($errors->has('confirm_password'))
                                            <span class="help-block alert alert-danger">
                                                <strong>{{ $errors->first('confirm_password') }}</strong>
                                            </span>
                                            @endif
                                        </div><br> <br>
                                    </div>
                                </div>
                                <span class="help-block"> <span class="colorRed"> *</span> mentioned fields are mandatory.</span>
                                <button type="submit" id="FormBtn" class="btn btn-info pull-right" style="margin-left: 20px;">{{$action}}</button>
                                <a href="{{url('admin/roleuser')}}" class="btn btn-default pull-right"  id="cancelBtn">Back</a>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    {{ Form::close() }}
                    <div class="clearfix"></div>
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
@section('script')
    @if(Session::has('message'))
        <script>
        $(function() {
            toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
        });
        </script>
    @endif
<script>
 var SITE_URL = "<?php echo URL::to('/'); ?>";
$.validator.addMethod("email", function(value, element) {
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
    }, "Please enter valid email.");
@if(!isset($roleuser))
                var rules = {"first_name":{required:true,},"last_name":{required:true,},"email":{required:true,},
                             "confirm_password":{required:true,equalTo:'#password',},
                              "password":{required:true,minlength: 6,maxlength: 20},
                              };
                var messages = {
                      "first_name":{
                          required:"Please enter first name.",
                      },
                      "last_name":{
                          required:"Please enter last name.",
                      },
                      "email":{
                          required:"Please enter email.",
                          remote:"Provided email already used by some one.",
                      },
                      "confirm_password":{
                          required:"Please enter confirm password.",
                          equalTo: "Please enter same as password.",
                      },
                      "password":{
                          required:"Please enter password.",
                      },
                    };
@else
var rules = {"first_name":{required:true,},"last_name":{required:true,},"email":{required:true,},
                             "confirm_password":{equalTo:'#password',},
                              "password":{minlength: 6,maxlength: 20},
                              };
                var messages = {
                      "first_name":{
                          required:"Please enter first name.",
                      },
                      "last_name":{
                          required:"Please enter last name.",
                      },
                      "email":{
                          required:"Please enter email.",
                          remote:"Provided email already used by some one.",
                      },
                      "confirm_password":{
                          equalTo: "Please enter same as password.",
                      },
                    };
@endif;

    $(document.body).on('click', "#FormBtn", function(){
        if ($("#roleForm").length){
            $("#roleForm").validate({
            errorElement: 'span',
                    errorClass: 'text-red',
                    ignore: [],
                    rules: rules,
                  messages: messages,
                    errorPlacement: function(error, element) {
                        if(element.is('select')){
                            error.appendTo(element.closest("div"));
                        }else{
                            error.insertAfter(element.closest(".form-control"));
                        }
                    },
            });
        }
    });

</script>
@endsection
