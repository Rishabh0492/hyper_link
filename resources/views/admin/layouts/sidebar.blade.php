
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->

    <ul class="sidebar-menu">
      <li class="header"></li>

      <li class="@if(Request::is('admin/dashboard')) active @endif treeview">
        <a href="{{url('admin/dashboard')}}">
          <i class="fa fa-tachometer"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="header"></li>
      @if(Auth::user()->can('user-list') || Auth::user()->can('user-create') 
      || Auth::user()->can('user-edit') || Auth::user()->can('user-delete')
      || Auth::user()->can('user-status-change' || Auth::user()->can('user-view')))
        <li class="@if(Request::is('admin/users') ||Request::is('admin/users/*') ) active @endif treeview">
          <a href="{{url('admin/users')}}">
            <i class="fa fa-user-circle-o"></i> <span>Users</span>
          </a>
        </li>
      @endif
      <li class="@if(Request::is('admin/roles') ||Request::is('admin/roles/*') ) active @endif treeview">
        <a href="{{url('admin/roles')}}">
          <i class="fa fa-globe"></i> <span>Manage Roles</span>
        </a>
      </li>

      <li class="@if(Request::is('admin/booking') ||Request::is('admin/booking/*') ) active @endif treeview">
        <a href="{{url('admin/booking')}}">
          <i class="fa fa-tasks"></i> <span>Booking</span>
        </a>
      </li>

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
