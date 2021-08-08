@section('title')
Permissions |
@endsection
@extends('admin.layouts.adminMaster')
@section('css')
<style>

.permission_section label{
    font-weight:500;
}
 .selectAll{
     padding-top:30px;
     text-align: center;
 }
 .categoryDiv{
     margin-top: 30px;
     background: #f7f7f7;
 }
 .categoryHeader{
     font-size: 25px;
 }
 .selectOnlyCategory {
    margin: 0 20px;
    font-size: 16px;
    text-transform: lowercase;
}
</style>
@endsection
@section('content')
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Role Permission</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Permissions</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <form class="form-horizontal" id="permissionForm" role="form" action="{{url('admin/permission/save')}}" method="post">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-3">
                                    <label for="">Select Role : </label>
                                </div>
                                <div class="col-lg-4">
                                    <select required class="form-control" name="roles" id="roles">
                                            <option value="">--Select Role--</option>
                                        @foreach($roles as $row)
                                            <option value="{{$row->id}}">{{$row->display_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <button type="submit" id="savePermission" class="btn btn-success">Save</button>
                                </div>
                            </div>
                            <div class="selectAll">
                                <label><input type="checkbox" name="select_all" id="select_all">Select all</label>
                            </div>
                            <div class="row permission_section">
                                @foreach($permissions as $key=>$row1)
                                    <div class="col-sm-12">
                                        <div class="categoryDiv">
                                            <p class="categoryHeader">{{$row1->category}}<label class="selectOnlyCategory"><input data-val="{{$key}}" class="selectOnlyCategory" name="selectOnlyCategory" type="checkbox">select all {{$row1->category}}</label></p>
                                        </div>
                                        @php $permissions = Helper::getPermissionByCategory($row1->category); @endphp
                                        @foreach($permissions as $row)
                                            <div class="col-lg-4 col-md-3 col-sm-3">
                                                <label><input type="checkbox" value="{{$row->id}}" name="permissions[]" class="minimal mr-2 all {{$key}}">&nbsp {{$row->display_name}}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </form>
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

@section("script")
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/jquery.validate.js')}}"></script>
<script src="{{URL::asset('resources/assets/custom/jQuery-validation-plugin/additional-methods.js')}}"></script>
@if(Session::has('message'))
        <script>
        $(function() {
            toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
        });
        </script>
    @endif
<script>
$(".selectOnlyCategory").change(function(){
    var value = $(this).data("val");
    if ($(this).is(':checked')) {
        $(".all."+value).prop('checked', true);
    }else{
        $(".all."+value+"").prop('checked', false);
    }
});
$("#select_all").change(function(){
    if ($(this).is(':checked')) {
        $(".all").prop('checked', true);

    }else{
        $(".all").prop('checked', false);
    }
});
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
 var SITE_URL = "<?php echo URL::to('/'); ?>";
$('#roles').on('change', function(){
    var id = $(this).val();
    $.ajax({
        type: 'POST',
        url: SITE_URL + '/admin/permission/getPermissions',
        data: { id :id },
        success: function(data) {
            $("input[type='checkbox']").prop('checked', false);
            if(data!=0){
                var parse = JSON.parse(data);
                $("input[type='checkbox']").prop('checked', false);
                $.each( parse, function( index, value ){
                    $("input[value='"+value+"']").prop('checked', true);
                });
            }
        }
    });
});
</script>
@endsection
