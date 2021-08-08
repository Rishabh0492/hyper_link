@extends('admin.layouts.app')
@section('title') Profile | @endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Profile Update</h1>
      <ol class="breadcrumb">
        <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#information" data-toggle="tab">Information</a></li>
              <li><a href="#password" data-toggle="tab">Password</a></li>
              <li><a href="#qrcode" data-toggle="tab">Qr code</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="information">
                <form class="form-horizontal" id="profileForm" method="post" action="{{route('profile.update',$user->id)}}" enctype="multipart/form-data">
                  @csrf
                  @method('PATCH')
                  <div class="form-group">
                    <label for="first_name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-5 jointbox">
                      <input type="text" class="form-control" name="first_name" id="first_name" value="{{$user->first_name}}" placeholder="First Name">
                      @if ($errors->has('first_name'))
                          <span class="help-block alert alert-danger">
                              <strong>{{ $errors->first('first_name') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="col-sm-5 ">
                      <input type="text" class="form-control" name="last_name" id="last_name" value="{{$user->last_name}}" placeholder="Last Name">
                      @if ($errors->has('last_name'))
                          <span class="help-block alert alert-danger">
                              <strong>{{ $errors->first('last_name') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control"  name="email" id="email" value="{{$user->email}}" placeholder="eg. abc@gmail.com" readonly>
                      @if ($errors->has('email'))
                          <span class="help-block alert alert-danger">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="user_mobile" class="col-sm-2 control-label">Phone number</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="user_mobile" id="user_mobile" value="{{$user->user_mobile}}" placeholder="eg. 9904132640">
                      @if ($errors->has('user_mobile'))
                          <span class="help-block alert alert-danger">
                              <strong>{{ $errors->first('user_mobile') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="profile_image" class="col-sm-2 control-label">Profile Photo</label>
                    <div class="col-sm-10">
                      <input type="file" class="" id="profile_image" name="profile_image" accept="image/*" >
                      @if ($errors->has('profile_image'))
                          <span class="help-block alert alert-danger">
                            <strong>{{ $errors->first('profile_image') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>


                  <div class="form-group">
                    <strong>Note: </strong> <span>The site needs to be HTTPS in order for user media / webcam to work!</span>
                    <label for="profile_image" class="col-sm-2 control-label">Capture Profile Photo</label>
                    <div class="col-sm-2">
                      <input type="hidden" class="" id="profile_image_snapshot" name="profile_image_snapshot">
                      <input type=button value="Take Snapshot" onClick="take_snapshot()" id="snapshot">
                    </div>
                    <div class="col-sm-8">
                      <div id="my_camera"></div>
                      <div id="results"></div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" id="createBtn" class="btn btn-danger">Update</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="tab-pane" id="password">
                <form class="form-horizontal" id="passwordForm" method="post" action="{{route('profile.update',$user->id)}}">
                   @csrf
                  @method('PATCH')
                  <div class="form-group">
                    <label for="old_password" class="col-sm-2 control-label">Current</label>
                    <div class="col-sm-10">
                      <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Your current password">
                      @if ($errors->has('old_password'))
                          <span class="help-block alert alert-danger">
                            <strong>{{ $errors->first('old_password') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="new_password" class="col-sm-2 control-label">New</label>
                    <div class="col-sm-10">
                      <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New password">
                      @if ($errors->has('new_password'))
                          <span class="help-block alert alert-danger">
                            <strong>{{ $errors->first('new_password') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="confirm_password" class="col-sm-2 control-label">Confirm</label>
                    <div class="col-sm-10">
                      <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm password">
                      @if ($errors->has('confirm_password'))
                          <span class="help-block alert alert-danger">
                            <strong>{{ $errors->first('confirm_password') }}</strong>
                          </span>
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" id="createBtn2" class="btn btn-danger">Update</button>
                    </div>
                  </div>
                </form>


                <div id="pswd_info">
                  <p class="pp"><b>Password must have:</b></p>
                  <ul>
                    <li id="length" class="invalid" style="color:#9e9fa1;">atleast 8 characters</li>
                    <li id="letter" class="invalid" style="color:#9e9fa1;">atleast 1 lowercase</li>
                    <li id="capital" class="invalid" style="color:#9e9fa1;">atleast 1 uppercase</li>
                    <li id="number" class="invalid" style="color:#9e9fa1;">atleast 1 number</li>
                    <li id="special" class="invalid" style="color:#9e9fa1;">atleast 1 special char</li>
                  </ul>
                </div>
              </div>
              <div class="tab-pane" id="qrcode">
                <form class="form-horizontal" id="upload-image-form" method="post" enctype="multipart/form-data">
                  @csrf
                 <div class="form-group">
                    <label for="profile_image" class="col-sm-2 control-label">Scan Qrcode Image</label>
                    <div class="col-sm-10">
                      <input type="file" class="" name="qrcode_image" accept="image/*" >
                    </div>
                  </div>

                  <div class="form-group">
                     <label for="profile_image" class="col-sm-2 control-label">Result</label>
                     <div class="col-sm-10">
                       <p id="qrcoderesult"></p>
                     </div>
                   </div>

                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" id="checkBtn" class="btn btn-success">Scan QR</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>

@endsection
@section('css')
  <style type="text/css">
    #pswd_info {
      position: absolute;
      bottom: 70px;
      left: 800px;
      width: 144px;
      padding: 5px;
      background: #fefefe;
      font-size: .750em;
      border-radius: 5px;
      box-shadow: 0 1px 3px #ccc;
      border: 1px solid #ddd;
      height: 165px;
    }
    #pswd_info h4 {
        margin:0 0 10px 0;
        padding:0;
        font-weight:normal;
    }
    #pswd_info::before {
        content: "\25b6";
        position:absolute;
        top:62px;
        right:95%;
        font-size:30px;
        line-height:14px;
        color:white;
        text-shadow:none;
        display:block;
    }
    .invalid {
        background:url('{{URL::asset("/resources/assets/img/invalid.png")}}') no-repeat 0 50%;
        padding-left:22px;
        line-height:24px;
        color:black;
    }
    .valid {
        background:url('{{URL::asset("/resources/assets/img/valid.png")}}') no-repeat 0 50%;
        padding-left:22px;
        line-height:24px;


    }
    #pswd_info {
        display:none;
    }
    .pp{
      font-size: 12px;
      font-style: bold;
    }
    #pswd_info ul{
      padding: 0px;
    }
    #pswd_info li{
        list-style: none;
    }
    #my_camera{
     border: 1px solid black;
     margin-left:30px;
    }
  </style>
@endsection
@section('script')
@if(Session::has('message'))
    <script>
        $(function() {
            toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
        });
    </script>
@endif
@if(Session::has('status'))

@endif
<script type="text/javascript" src="{{URL::asset('resources/assets/webcam/webcamjs/webcam.min.js')}}"></script>
<script>
    $(document).ajaxStart(function() {
        Pace.restart();
    });

    Webcam.set({
     width: 320,
     height: 240,
     image_format: 'jpeg',
     jpeg_quality: 90
    });
    Webcam.attach( '#my_camera' );

    <!-- Code to handle taking the snapshot and displaying it locally -->
    function take_snapshot() {
      $('#my_camera').css('display','none');
      $('#snapshot').css('display','none');
    // take snapshot and get image data
    Webcam.snap( function(data_uri) {
      document.getElementById('profile_image_snapshot').value = data_uri;
     // display results in page
     document.getElementById('results').innerHTML =
     '<img src="'+data_uri+'"/>';
     } );
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var SITE_URL = "<?php echo URL::to('/'); ?>";
    $('#upload-image-form').submit(function(e) {
      e.preventDefault();
      $('#qrcoderesult').html('');
       let formData = new FormData(this);
       $.ajax({
             type:'POST',
             data: formData,
             url  : SITE_URL+'/admin/qrcode-scan',
             contentType: false,
             processData: false,
             success: (response) => {
             if (response) {
               this.reset();
               $('#qrcoderesult').html(response);
             }
           },
           error: function(response){
              $('#qrcoderesult').html('Please select proper qrcode image');
           }
       });
     });

    $(document).ready(function() {
      $('input[type=password]').keyup(function() {
      	$('#password_confirmation').on('keyup',function(){
          $('#pswd_info').hide();
      });
          // keyup event code here
      });
      $('input[type=password]').focus(function() {
          // focus code here
      });
      $('input[type=password]').blur(function() {

        $('#new_password').removeClass('valid');
          // blur code here
      });
       $('input[type=password]').keyup(function() {
          // keyup code here
      }).focus(function() {
          // focus code here

      }).blur(function() {
          // blur code here
      });

      $('#new_password').keyup(function() {
      // keyup code here
      var pswd = $(this).val();
      //validate the length
      if ( pswd.length < 8 ) {
          $('#length').removeClass('valid').addClass('invalid');
      } else {
          $('#length').removeClass('invalid').addClass('valid');
      }

      //validate letter
      if ( pswd.match(/[a-z]/) ) {
          $('#letter').removeClass('invalid').addClass('valid');
      } else {
          $('#letter').removeClass('valid').addClass('invalid');
      }

      //validate capital letter
      if ( pswd.match(/[A-Z]/) ) {
          $('#capital').removeClass('invalid').addClass('valid');
      } else {
          $('#capital').removeClass('valid').addClass('invalid');
      }

      //validate number
      if ( pswd.match(/\d/) ) {
          $('#number').removeClass('invalid').addClass('valid');
      } else {
          $('#number').removeClass('valid').addClass('invalid');
      }
      if (/^[a-zA-Z0-9- ]*$/.test(pswd) == false) {
          $('#special').removeClass('invalid').addClass('valid');
      } else {
          $('#special').removeClass('valid').addClass('invalid');
      }

      }).focus(function() {
          $('#pswd_info').show();
      }).blur(function() {
          $("#pswd_info").hide();

      });
    });
    $('#password_confirmation').on('click',function(){
     $('#pswd_info').hide();
    });
</script>

<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/jquery.validate.js')}}"></script>
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/additional-methods.js')}}"></script>
<script>
  $.validator.addMethod("pass", function(value, element) {
    return /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/.test(value);
  }, "Please enter valid password.");
  $(document.body).on('click', "#createBtn", function(){
      if ($("#profileForm").length){
          $("#profileForm").validate({
                  errorElement: 'span',
                  errorClass: 'text-red',
                  ignore: [],
                  rules: {
                      "first_name":{
                          required:true,
                      },
                      "last_name":{
                          required:true,
                      },
                      "email":{
                          required:true,
                          email:true
                      },
                      "user_mobile":{
                          required:true,
                          number: true,
                          minlength: 10,
                          maxlength: 12
                      },
                  },
                  messages: {
                      "first_name":{
                          required:"Please enter first name."
                      },
                      "last_name":{
                          required:"Please enter last name."
                      },
                      "email":{
                          required:"Please enter email."
                      },
                      "user_mobile":{
                          required:"Please enter mobile no."
                      },
                  },
                  errorPlacement: function(error, element) {
                  error.insertAfter(element.closest(".form-control"));
                  },
          });
      }
  });
  $.validator.addMethod("password_length", function(value, element) {
      return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^.()_-])[A-Za-z\d@$!%*?&#^.()_-]{8,18}$/.test(value);
    }, "Enter combination of at least 8 number, letters and special characters.");
  $(document.body).on('click', "#createBtn2", function(){
      if ($("#passwordForm").length){
          $("#passwordForm").validate({
                  errorElement: 'span',
                  errorClass: 'text-red',
                  ignore: [],
                  rules: {
                      "old_password":{
                          required:true
                      },
                      "new_password":{
                          required:true,
                          password_length:true,
                      },
                      "confirm_password":{
                          required:true,
                          equalTo: "#new_password"
                      },
                  },
                  messages: {
                      "old_password":{
                          required:"Please enter current password."
                      },
                      "new_password":{
                          required:"Please enter new password.",
                          pass:"Password should contain number,characters,special character and atleast one capital letter."
                      },
                      "confirm_password":{
                          required:"Please enter confirm password.",
                          equalTo:"Please enter same as new password."
                      },
                  },
                  errorPlacement: function(error, element) {
                  error.insertAfter(element.closest(".form-control"));
                  },
          });
      }
  });
</script>
@endsection
