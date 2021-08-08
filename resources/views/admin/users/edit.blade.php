
@extends('admin.layouts.app')
@section('title') Create User | @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Update User</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{url('admin/users')}}"> Users</a></li>
            <li class="active">Update User</li>
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
                    <form class="form-horizontal" id="empForm" action="{{url('admin/users').'/'.$user->id}}" method="post" enctype="multipart/form-data">
                        @method('PUT')
                    <div class="col-sm-8">
                            {{ csrf_field() }}
                            <div class="box-body">
                                <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}"/>
                                <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="first_name">Full Name <span class="colorRed"> *</span></label>
                                    <div class="col-sm-4 jointbox">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="@if(!empty(old('first_name'))){{old('first_name')}}@else{{$user->first_name}}@endif"/>
                                        @if ($errors->has('first_name'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4 ">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="@if(!empty(old('last_name'))){{old('last_name')}}@else{{$user->last_name}}@endif"/>
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
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="@if(!empty(old('email'))){{old('email')}}@else{{$user->email}}@endif"/>
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
                                        <input type="text" class="form-control" id="user_mobile" name="user_mobile" placeholder="Mobile number" value="@if(!empty(old('user_mobile'))){{old('user_mobile')}}@else{{$user->user_mobile}}@endif"/>
                                        @if ($errors->has('user_mobile'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('user_mobile') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="country">DOB </label>
                                    <div class="col-sm-8">
                                        <input readonly="true" type="text" class="form-control" id="dob" name="dob" placeholder="DOB" value="@if(!empty(old('dob'))){{old('dob')}}@else{{$user->dob}}@endif"/>
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
                                    <select id="country" name="country" class="form-control">  
                                         @foreach($getCountry as $row)
                                          <option @if($row->country_id == $user->country) selected @endif value="{{$row->country_id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                        @if ($errors->has('country'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('country') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="state">State <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <select required name="state" class="form-control" id="state">

                                            @foreach($getState as $row)
                                            <option @if($row->state_id == $user->state) selected @endif value="{{$row->state_id}}">{{$row->name}}</option>
                                            @endforeach
                                        </select>
                                      @if ($errors->has('state'))
                                      <span class="help-block alert alert-danger">
                                          <strong>{{ $errors->first('state') }}</strong>
                                      </span>
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="city">City <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                    <select required name="city" class="form-control" id="city">
                                        @foreach($getCity as $row)
                                            <option @if($row->city_id == $user->city) selected @endif value="{{$row->city_id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                        @if ($errors->has('city'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('city') }}</strong>
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
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-top: 15px;">
                                    <div class="vc_column-inner ">
                                        <label class="cabinet center-block">
                                            <figure>
                                                <img src="@if($user->profile_image){{ $user->profile_image}} @else {{ URL::asset('/resources/assets/img/user.png')}} @endif" class="gambar old_profile_imageSub" id="item-img-output"  name="avatar" width="300px" style="display: initial;"/>
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
    $("#cancelBtn").click(function () {
        window.location.href = "{{url('admin/users')}}";
    });
$(document).ajaxStart(function() { Pace.restart(); });
 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    $.validator.addMethod("pass", function(value, element) {
        return this.optional(element) || /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/.test(value);
    }, "Please enter valid password.");
    $(document.body).on('click', "#updateBtn", function(){
        if ($("#empForm").length){
            $("#empForm").validate({
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
                          email:true
                        },
                        "user_mobile":{
                          required:true,
                            number:true,
                            minlength:10,
                            maxlength:12
                        },
                        "dob":{
                          required:true,
                        },
                        "password":{
                          minlength: 6,
                          maxlength: 20,
                          pass:true
                        },
                        "confirm_password":{
                          equalTo:'#password',
                        },
                        "country":{
                          required:true,
                        },
                        "state":{
                          required:true,
                        },
                        "city":{
                          required:true,
                        },
                        'short_introduction': {
                            required: true,
                            minlength: 2
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
    var today = new Date();
    $('#dob').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true,
      endDate: "today",
      maxDate: today
    });
    var SITE_URL = "<?php echo URL::to('/'); ?>";
$('#country').on('change', function(){
    $("#country-error").hide();
    var id = $('#country').val();
    $.ajax({
        type: 'POST',
        url: SITE_URL + '/getState',
        data: { id :id },
        success: function(data) {
            $('#state').html("");
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
            $('#city').html("");
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
</script>

<script>
function readURL(input) {
    var old_profile_image = $('#old_profile_image').val();
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.old_profile_imageSub')
                .attr('src', e.target.result)
                .width(125)
                .height(125);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

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
