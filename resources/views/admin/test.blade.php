<?php
 $menuList = [
                [
                  "href" => "admin/dashboard",
                  "class" => "fa-tachometer",
                  "name" => "Dashboard",
                ],
                [
                  "href" => "admin/users",
                  "class" => "a-user-circle-o",
                  "name" => "Users",
               ]
               ];
               
?>
<ul class="sidebar-menu">
      <li class="header"></li>


@foreach ($menuList as $key => $value) 

<li class="@if(Request::is($value['href'])) active @endif treeview">
        <a href="{{url($value['href'])}}">
          <i class="fa {{$value['class']}}"></i> <span>{{$value['name']}}</span>
        </a>
      </li>
@endforeach


