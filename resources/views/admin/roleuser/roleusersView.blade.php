@section('title')
User Detail |
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
@extends('admin.layouts.adminMaster') @section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>User Detail</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{url('admin/users')}}"> Users</a></li>
            <li class="active">Detail</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <!-- Horizontal Form -->
                <div class="box ">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" id="empForm" action="{{url('admin/roleusers/update')}}" method="post" enctype="multipart/form-data">
                    <div class="col-sm-8">
                            {{ csrf_field() }}
                            <div class="box-body">
                                <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}"/>

                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="email">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="@if(!empty(old('email'))){{old('email')}}@else{{$user->email}}@endif"/>
                                        @if ($errors->has('email'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="password">Password</label>
                                    <div class="col-sm-4 jointbox">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="{{old('password')}}"/>
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
                                </div>
                            </div>
                            <span class="help-block"> <span class="colorRed"> *</span> mentioned fields are mandatory.</span>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <button type="button" class="btn btn-default" id="cancelBtn">Back</button>
                                <button type="submit" id="updateBtn" class="btn btn-info pull-right">Update</button>
                            </div>
                            <!-- /.box-footer -->

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

<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/jquery.validate.js')}}"></script>
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/additional-methods.js')}}"></script>
<script src="{{ URL::asset('/resources/assets/admin/plugins/lightbox2-master/dist/js/lightbox.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/prism.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/sweetalert.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/croppie.js')}}"></script>
<script src="{{URL::asset('/resources/assets/custom/image_cropping/main.js')}}"></script>
<script>
    $("#cancelBtn").click(function () {
        window.location.href = "{{url('admin/roleuser')}}";
    });
$(document).ajaxStart(function() { Pace.restart(); });
 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    $(document.body).on('click', "#updateBtn", function(){
        var id = "{{$user->id}}";
        if ($("#empForm").length){
            $("#empForm").validate({
              errorElement: 'span',
                      errorClass: 'text-red',
                      ignore: [],
                      rules: {
                        "email":{
                            remote: {
                                url: SITE_URL + '/check-email-exsist',
                                type: "get",
                                data:{id:id,type:'1'}
                            }
                        },
                        "confirm_password":{
                          equalTo:'#password',
                        },
                        "password":{
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
    var SITE_URL = "<?php echo URL::to('/'); ?>";
</script>
@endsection
