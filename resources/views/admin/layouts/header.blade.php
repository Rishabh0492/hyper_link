<header class="main-header">
<?php $user = Auth::user(); 
      $first_name = ucfirst($user->first_name);
      $last_name = ucfirst($user->last_name);
      $profile_image = $user->profile_image;
      $profile_image_url = $profile_image == "" ? 
                          '/resources/assets/admin/dist/img/user-placeholder.jpg':
                          $profile_image;
?>
  <!-- Logo -->
  <a href="{{url('admin/dashboard')}}" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>{{ config('app.short_name') }}</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>{{ config('app.name') }}</b></span>
  </a>

  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="{{ URL::asset($profile_image_url)}}" alt="{{$first_name}}" class="user-image">
            <span class="hidden-xs">{{ $first_name }} {{ $last_name }}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
                <img src="{{ URL::asset($profile_image_url)}}" alt="{{$first_name}}" class="img-circle">
              <p>
                {{ $first_name .' '. $last_name }}
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="{{url('/admin/profile')}}" class="btn btn-default btn-flat">Profile</a>
              </div>

              <div class="pull-right">
                <a href="{{ route('logout') }}" class="btn btn-default btn-flat"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
      </ul>
    </div>
  </nav>
</header>
