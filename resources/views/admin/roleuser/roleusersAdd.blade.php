@section('title')
Add New User |
@endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('/resources/assets/admin/plugins/lightbox2-master/dist/css/lightbox.css')}}">
<link rel="stylesheet" href="{{ URL::asset('/resources/assets/custom/image_cropping/prism.css')}}">
<link rel="stylesheet" href="{{ URL::asset('/resources/assets/custom/image_cropping/sweetalert.css')}}">
<link rel="stylesheet" href="{{ URL::asset('/resources/assets/custom/image_cropping/croppie.css')}}">

<style>
    .cabinet figure{
        text-align: center;
    }
    .old_profile_imageSub{
        max-width: 100%;
    }
    #upload-demo{
        height: 500px;
    }
    .cabinet figure{
        text-align: center;
    }
    .profile_image_showInput{
        margin-top: 2px;
    }
</style>
@endsection
@extends('admin.layouts.adminMaster')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add New User</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Add New User</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <!-- Horizontal Form -->
                <div class="box">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" id="userForm" role="form" action="{{url('admin/roleusers/new')}}" method="post" enctype="multipart/form-data" >
                        <div class="col-sm-8">
                            {{ csrf_field() }}
                            <div class="box-body">

                              <div class="form-group">
                                <label  class="col-sm-4 control-label" for="geo_hub_name">Select Role <span class="colorRed"> *</span></label>
                                <div class="col-sm-8">
                                  <select class="form-control" id="name" name="name" data-placeholder="select role">
                                    @foreach($roles as $type =>$role)
                                    <option value="{{ $role->id }}">
                                      {{ $role->name }}
                                    </option>
                                    @endforeach
                                  </select>
                                  @if ($errors->has('name'))
                                  <span class="help-block alert alert-danger">
                                    <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                                  @endif
                                </div>
                              </div>

                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="email">Email <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{old('email')}}"/>
                                        @if ($errors->has('email'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="password">Password <span class="colorRed"> *</span></label>
                                    <div class="col-sm-4 jointbox">
                                        <input autocomplete="new-password" type="password" class="form-control" id="password" name="password" placeholder="Password" value="{{old('password')}}"/>
                                        @if ($errors->has('password'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4 ">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" value="{{old('confirm_password')}}"/>
                                        @if ($errors->has('confirm_password'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('confirm_password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
    
                                <span class="help-block"> <span class="colorRed"> *</span> mentioned fields are mandatory.</span>
                                    <button type="button" class="btn btn-default"  id="cancelBtn">Close</button>
                                    <button type="submit" id="createBtn" class="btn btn-info pull-right">Create</button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </form>
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
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/jquery.validate.js')}}"></script>
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/additional-methods.js')}}"></script>
<script src="{{ URL::asset('/resources/assets/admin/plugins/lightbox2-master/dist/js/lightbox.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/prism.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/sweetalert.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/croppie.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/main.js')}}"></script>
    @if(Session::has('message'))
        <script>
        $(function() {
            toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
        });
        </script>
    @endif
<script>
$(document).ajaxStart(function() { Pace.restart(); });
 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var SITE_URL = "<?php echo URL::to('/'); ?>";
$.validator.addMethod("email", function(value, element) {
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
    }, "Please enter valid email.");
    $(document.body).on('click', "#createBtn", function(){
        if ($("#userForm").length){
            $("#userForm").validate({
            errorElement: 'span',
                    errorClass: 'text-red',
                    ignore: [],
                    rules: {
                      "email":{
                        required:true,
                          // remote: {
                          //     url: SITE_URL + '/check-email-exsist',
                          //     type: "get"
                          // }
                      },
                      "confirm_password":{
                        required:true,
                        equalTo:'#password',
                      },
                      "password":{
                        required:true,
                        minlength: 6,
                        maxlength: 20
                      },
                  },
                  messages: {
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
                    },
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
$("#cancelBtn").click(function () {
    window.location.href = "{{url('admin/roleusers')}}";
});
</script>
@endsection
