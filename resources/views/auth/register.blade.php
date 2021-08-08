@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" id="dataForm" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>

                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>

                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" id="submitForm" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                        <p class="mt-3 mb-1">
                            <a href="{{ route('login') }}">Login</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        var SITE_URL = "<?php echo URL::to('/'); ?>";
        
        $.validator.addMethod("email", function(value, element) {
              return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
        }, "Please enter valid email.");

        $.validator.addMethod("lettersonlys", function(value, element) {
            return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
        }, "Letters only please");

        $.validator.addMethod("password_length", function(value, element) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^.()_-])[A-Za-z\d@$!%*?&#^.()_-]{8,18}$/.test(value);
        }, "Enter combination of at least 8 number, letters ,special character and atleast one capital letter.");

        $(document.body).on('click',"#submitForm",function(){
            if($("#dataForm").length){
                $("#dataForm").validate({
                // onfocusout: false,
                errorElement: 'span',
                errorClass: 'text-danger',
                ignore: [],
                    rules: {
                        "first_name":{
                            required:true,
                            lettersonlys:true,
                            minlength: 2,
                            maxlength: 30,
                        },
                        "last_name":{
                            required:true,
                            lettersonlys:true,
                            minlength: 2,
                            maxlength: 30,
                        },
                        "email":{
                            required:true,
                            email:true,
                            remote: {
                                url: SITE_URL + '/check-email-exsist',
                                type: "get"
                            }
                        },
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
                        "email":{
                            required:'Please enter email address.',
                            remote:"Provided email already used by some one.",
                        },
                        "first_name":{
                            required:"Please enter first name.",
                            lettersonlys:"Please enter only alphabetic characters.",
                            maxlength:"Please enter not more than 30 characters.",
                        },
                        "last_name":{
                            required:"Please enter last name.",
                            lettersonlys:"Please enter only alphabetic characters.",
                            maxlength:"Please enter not more than 30 characters.",
                        },
                        "password_confirmation":{
                            required:"Please enter confirm password.",
                            equalTo: "Please enter same as password.",
                        },
                        "password":{
                            required:"Please enter password.",
                        },
                    },
                    errorPlacement: function(error, element) {
                        if(element.attr('name') == 'country_id'
                        || element.attr('name') == 'state_id'
                        || element.attr('name') == 'city_id'){
                            error.insertAfter(element.closest(".form-group"));
                        }else{
                            error.insertAfter(element.closest(".form-control"));
                        }
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
            var input = $("#password_confirmation");
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
@endsection