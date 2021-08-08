@extends('layouts.app')

@section('content')
<div class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
          <a><b>{{config('app.name')}}</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form method="POST" action="{{ route('login') }}">
                  @csrf
                    @if (session('message'))
                        <div class="help-block alert alert-{{session('alert-class')}} text-left">
                            <span>{{session('message')}}</span>
                        </div>
                    @endif
                    <div class="input-group mb-3">
                      <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                      <div class="input-group-append">
                        <div class="input-group-text">
                          <span class="fas fa-envelope"></span>
                        </div>
                      </div>
                      @if ($errors->has('email'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="input-group mb-3">
                      <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>
                      <div class="input-group-append">
                        <div class="input-group-text">
                          <span class="fas fa-lock"></span>
                        </div>
                      </div>
                      @if ($errors->has('password'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="row">
                      <div class="col-8">
                        <div class="icheck-primary">
                            <input id="remember" type="checkbox" name="remember" class="form-check-input"
                             {{ old("remember") ? "checked" : ""  }}>

                          <label for="remember">
                            {{ __('Remember Me') }}
                          </label>
                        </div>
                      </div>
                      <!-- /.col -->
                      <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('Login') }}</button>
                      </div>
                      <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
</div>
<!-- /.login-box -->
@endsection
@section('css')
<style>
    .error-messages{
        text-align: center;
        border: 1px solid;
        border-radius: 2px;
        margin-bottom: 20px;
        /* background: #eff8f1!important; */
    }
</style>
@endsection
