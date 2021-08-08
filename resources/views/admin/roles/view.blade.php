@extends('admin.layouts.app')
@section('title')  Role Detail | @endsection
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Role Detail</h1>
    <ol class="breadcrumb">
      <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{route('roles.index')}}"> Role</a> </li>
      <li class="active">Role Detail</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-sm-12">
        <!-- Horizontal Form -->
        <div class="box ">
          <!-- /.box-header -->
          <div class="col-sm-12">
                <div class="box-body">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="personal">
                                <div class="table-responsive">
                                    <table class="table table-user-information">
                                        <tbody>
                                            <tr>
                                                <td><strong>Name</strong></td>
                                                <td class="text-primary">{{ $role->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Permissions</strong></td>
                                                <td class="text-primary">
                                                    @if(count($rolePermissions))
                                                        @foreach($rolePermissions->groupBy('category') as $value)
                                                            @if(count($value))
                                                                <div class="categoryDiv">
                                                                    <p class="categoryHeader">{{$value[0]->category}}</p>
                                                                </div>
                                                                @foreach($value as $data)
                                                                    <label class="label label-success">{{ $data->name }},</label>
                                                                @endforeach
                                                            @endif
                                                            <br  />
                                                            @endforeach
                                                        {{-- @foreach($rolePermissions as $v)
                                                            <div class="categoryDiv">
                                                                <p class="categoryHeader">{{$v->category}}</p>
                                                            </div>
                                                            <label class="label label-success">{{ $v->name }},</label>
                                                        @endforeach --}}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="" style="border-top:0">
                    <div class="box-footer">
                        <a type="button" href="{{route('roles.index')}}" id="cancelBtn" class="btn btn-default pull-right">Back</a>
                    </div>
                </div>
            </div>
            <div class="clearfix">
            </div>
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </section>
    </div>
@endsection
@section('css')
<style>
    .permission_section label{
        font-weight:500;
    }
    .categoryDiv{
        margin-top: 10px;
        background: #f7f7f7;
    }
    .categoryHeader{
        font-size: 17px;
    }
    .selectOnlyCategory {
        margin: 0 20px;
        font-size: 16px;
        text-transform: lowercase;
    }
    .roles-name{
        font-size: 12px;
    }
</style>
@endsection