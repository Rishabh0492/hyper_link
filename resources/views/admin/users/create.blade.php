@extends('admin.layouts.app')
@section('title') Create User | @endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Create User</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Create User</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <!-- Horizontal Form -->
                <div class="box">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" id="userForm" role="form" action="{{url('admin/users')}}" method="post" enctype="multipart/form-data" >
                        <div class="col-sm-8">
                            @csrf
                            <div class="box-body">

                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="first_name">Full Name <span class="colorRed"> *</span></label>
                                    <div class="col-sm-4 jointbox">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{old('first_name')}}"/>
                                        @if ($errors->has('first_name'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4 ">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="{{old('last_name')}}"/>
                                        @if ($errors->has('last_name'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('last_name') }}</strong>
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

                                <div class="form-group {{ $errors->has('user_mobile') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="user_mobile">Mobile no <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <input type="number" maxlength="12" class="form-control" id="user_mobile" name="user_mobile" placeholder="Mobile number" value="{{old('user_mobile')}}"/>
                                        @if ($errors->has('user_mobile'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('user_mobile') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="dob">DOB <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <input readonly="true" type="text" class="form-control" id="dob" name="dob" placeholder="DOB" value="{{old('dob')}}"/>
                                        @if ($errors->has('dob'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('dob') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="country">Country <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="country">

                                        @if ($errors->has('country'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('country') }}</strong>
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
                                </div>
                                <span class="help-block"> <span class="colorRed"> *</span> mentioned fields are mandatory.</span>
                                    <button type="button" class="btn btn-default"  id="cancelBtn">Close</button>
                                    <button type="submit" id="createBtn" class="btn btn-info pull-right">Create</button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-top: 15px;">
                                    <div class="vc_column-inner ">
                                        <label class="cabinet center-block">
                                            <figure style="text-align: center;">
                                                <img src="{{ URL::asset('/resources/assets/img/user.png')}}" class="gambar old_profile_imageSub" id="item-img-output"  name="avatar" width="300px" style="display: initial;" />
                                            </figure>
                                            <p></p>
                                            <input type="hidden" name="main_image" value="" id="main_image" style="">
                                            <input type="file" class="item-img profile_image_showInput" id="profile_image" accept="image/*" name="file_photo"/>
                                            <span class="changeImage profilechangeImage"><i class="fa fa-edit"></i>Change Image</span>
                                        </label>

                                        <div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog  ">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="upload-demo" class="center-block"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-default" id="Flip">Flip</button>
                                                        {{-- <button class="btn btn-default" id="rotate" data-deg="-90">Rotate</button>--}}
                                                        <button type="button" id="cropImageBtn" class="btn btn-primary">Crop</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
    var today = new Date();
    $('#dob').datepicker({
        onSelect: function(dateText) {
            $("#dob-error").hide();
        },
      format: 'dd-MM-yyyy',
      autoclose:true,
      endDate: "today",
      maxDate: today 
    });
var SITE_URL = "<?php echo URL::to('/'); ?>";
$.validator.addMethod("email", function(value, element) {
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
    }, "Please enter valid email.");
    $.validator.addMethod("pass", function(value, element) {
      return /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/.test(value);
    }, "Please enter valid password.");
    $(document.body).on('click', "#createBtn", function(){
        if ($("#userForm").length){
            $("#userForm").validate({
            errorElement: 'span',
                    errorClass: 'text-red',
                    ignore: [],
                    rules: {
                      "first_name":{
                          required:true,
                          minlength: 2,
                          maxlength: 20
                      },
                      "last_name":{
                          required:true,
                          minlength: 2,
                          maxlength: 20
                      },
                      "email":{
                        required:true,
                        email:true,
                          remote: {
                              url: SITE_URL + '/check-email-exsist',
                              type: "get"
                          }
                      },
                      "user_mobile":{
                        required:true,
                          number:true,
                          minlength:10,
                          maxlength:12,
                          remote: {
                              url: SITE_URL + '/check-number-exsist',
                              type: "get"
                          }
                      },
                      "dob":{
                        required:true,
                      },
                      "confirm_password":{
                        required:true,
                        equalTo:'#password',
                      },
                      "password":{
                        required:true,
                        pass:true,
                      },
                      "country":{
                        required:true
                      },
                      "state":{
                        required:true
                      },
                      "city":{
                        required:true
                      },
                  },
                  messages: {
                       "email":{
                            required:"Please enter email.",
                            remote:"Provided email already used by some one.",

                        },
                          "first_name":{
                            required:"Please enter first name.",
                        },
                          "last_name":{
                            required:"Please enter last name.",
                        },
                          "user_mobile":{
                            required:"Please enter mobile number.",
                            minlength: "Please enter at least 10 digits.",
                              maxlength: "Please do not enter more than 15 digits.",
                              remote:"Provided number has already been used by someone else.",
                        },
                        "dob":{
                          required:"Please select date of birth.",
                        },
                          "confirm_password":{
                            required:"Please enter confirm password.",
                            equalTo: "Please enter same as password.",
                        },
                        "country":{
                          required:"Please select country.",
                        },
                        "state":{
                          required:"Please select state.",
                        },
                        "city":{
                          required:"Please select city.",
                        },
                        "password": {
                          required:"Please enter password",
                          pass:"Password should contain number,characters,special character and atleast one capital letter."
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
$('#country').on('change', function(){
    $("#country-error").hide();
    var id = $('#country').val();
    $.ajax({
        type: 'POST',
        url: SITE_URL + '/getState',
        data: { id :id },
        success: function(data) {
            $('#city').html("");
            $('#state').html(data);
        }
    });
});
$('#state').on('change', function(){
    $("#state-error").hide();
    var id = $('#state').val();
    $.ajax({
        type: 'POST',
        url: SITE_URL + '/getCity',
        data: { id :id },
        success: function(data) {
            $('#city').html(data);
        }
    });
});
$('#city').on('change', function(){
    $("#city-error").hide();
});
$(document).ready(function() {
            $("#country").select2({
                placeholder: "Select a Country",
                allowClear: true,
            });

            $("#state").select2({
                placeholder: "Select a state",
                allowClear: true,
            });

            $("#city").select2({
                placeholder: "Select a State",
                allowClear: true,
            });
        });
$("#cancelBtn").click(function () {
    window.location.href = "{{url('admin/users')}}";
});
</script>
<script type="text/javascript">
    // Start upload preview image
    var FLIP = 2;
    var NORMAL = 1;
    var orientation = NORMAL;
    var $uploadCrop1, tempFilename, rawImg, imageId;
    var fileTypes = ['jpg', 'jpeg', 'png'];
    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var file = input.files[0]; // Get your file here
            var fileExt = file.type.split('/')[1]; // Get the file extension
            if (fileTypes.indexOf(fileExt) !== -1) {
                reader.onload = function (e) {
                    $('.upload-demo').addClass('ready');
                    $('#cropImagePop').modal('show');
                    rawImg = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);

            }else{
                swal("Only JPEG, PNG, JPG file types are supported");
            }
        }
        else {
            swal("Sorry - something went wrong");
        }
    }

    $uploadCrop1 = $('#upload-demo').croppie({
        viewport: {
            width: 480,
            height: 270,
        // type: 'square'
        },
        enableOrientation: true,
        enforceBoundary: false,
        enableExif: true,
        enableResize:true,
    });
    $('#cropImagePop').on('shown.bs.modal', function(){
        // alert('Shown pop');
        $uploadCrop1.croppie('bind', {
            url: rawImg
            }).then(function(){
                // console.log('jQuery bind complete');
            });
    });
    $('#Flip').click(function() {
            orientation = orientation == NORMAL ? FLIP : NORMAL;
            $uploadCrop1.croppie('bind', {
            url: rawImg,
            orientation: orientation,
        });
    });
    $('#rotate').click(function() {
        $uploadCrop1.croppie('rotate', parseInt($(this).data('deg')));
    });

    $('.item-img').on('change', function () { imageId = $(this).data('id'); tempFilename = $(this).val();
    $('#cancelCropBtn').data('id', imageId); readFile(this); });
    $('#cropImageBtn').on('click', function (ev) {
            $uploadCrop1.croppie('result', {
            type: 'base64',
            format: 'jpeg',
            size: {width: 1280, height: 720}
        }).then(function (resp) {
            $('#item-img-output').attr('src', resp);
            $('#main_image').attr('value', resp);
            $('#cropImagePop').modal('hide');
        });
    });
    // End upload preview image
</script>
@endsection
