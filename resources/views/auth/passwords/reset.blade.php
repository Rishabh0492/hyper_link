@extends('layouts.app')

@section('content')
<div class="h-100 for-bg d-flex align-items-center">
  <div class="container">
    <div class="login-box">
      <div class="login-box-body">
        @if (session('status'))
          <div class="alert alert-success" role="alert">
            {{ session('status') }}
          </div>
        @endif
        <form class="" method="POST" id="passwordReset" action="{{ route('password.update') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          <div class="form-group has-feedback">
            <input id="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus style="border-radius: 0!important;">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('email'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>
          <div class="form-group has-feedback">
            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required oninvalid="this.setCustomValidity('Please Enter Your Password')" placeholder="Password" value="{{old('password')}}" style="border-radius: 0!important;">
            <span toggle="#password-field" class="fa fa-fw fa-eye pass_field_icon toggle-password" style="float:right;margin-right: 8px!important;margin-top: -25px;"></span>
            @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
            @endif
          </div>


      <div class="form-group has-feedback">


              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Confirm Password" style="border-radius: 0!important;">
               <span toggle="#password-field" class="fa fa-fw fa-eye pass_field_icon toggle-confirm-password" style="float:right;margin-right: 8px!important;margin-top: -25px;"></span>

      </div>
      <div class="row">
      <div class="col-12">


              <button type="submit" id ="resetBtn" class="btn btn-primary btn-block btn-flat">
                  {{ __('Reset Password') }}

          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('css')

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

    $.validator.addMethod("password_length", function(value, element) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^.()_-])[A-Za-z\d@$!%*?&#^.()_-]{8,18}$/.test(value);
    }, "Enter combination of at least 8 number, letters and special characters.");

    $(document.body).on('click',"#resetBtn",function(){
        if($("#passwordReset").length){
            $("#passwordReset").validate({
            onfocusout: false,
            errorElement: 'span',
            errorClass: 'text-danger',
            ignore: [],
                rules: {
                    // "email":{
                    //   required:true,
                    // },
                    "password":{
                        required:true,
                        password_length:true,
                    },
                    "password_confirmation":{
                        required:true,
                        equalTo:'#password',
                    },

                    },
                    messages: {
                        "password_confirmation":{
                            required:"Please enter confirm password.",
                            equalTo: "Please enter same as password.",
                        },
                        "password":{
                            required:"Please enter password.",
                        },
                        "email":{
                          required:"Please enter email.",
                        },

                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element.closest(".form-control"));
                    },
                        submitHandler: function(form,e) {
                            e.preventDefault();
                            $("#submitForm").html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>');
                            form.submit();
                        },
                });
        }
    });
    $("body").on('click', '.toggle-password', function() {
      $(this).toggleClass("fa-eye-slash fa-eye");
      var input = $("#password");
      if (input.attr("type") === "password") {
          input.attr("type", "text");
      } else {
          input.attr("type", "password");
      }
    });
    $("body").on('click', '.toggle-confirm-password', function() {
      $(this).toggleClass("fa-eye-slash fa-eye");
      var input = $("#password-confirm");
      if (input.attr("type") === "password") {
          input.attr("type", "text");
      } else {
          input.attr("type", "password");
      }
    });
</script>

@endsection
