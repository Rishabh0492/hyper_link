@section('title')
Role Edit |
@endsection

@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Role Edit</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Role Edit</li>
        </ol>
    </section>
    
    <section class="content">
        <div class="row">
            <form class="" id="dataForm" role="form" action="{{route('roles.update', $role->id)}}" method="post" enctype="multipart/form-data" >
                {{method_field('PATCH')}}
                {{ csrf_field() }}
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Role</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label  class=" control-label" for="name">Role Name <span class="colorRed"> *</span></label>
                                <div class=" jointbox">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Role Name in lower case" value="@if($role->name) {{$role->name}} @else {{old('name')}} @endif"/>
                                    @if ($errors->has('name'))
                                    <span class="help-block alert alert-danger">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('permission') ? ' has-error' : '' }}">
                                <label  class="control-label" for="name">Permission <span class="colorRed"> *</span></label>
                                <div>
                                    @foreach($permission as $key=>$row1)
                                        <div class="col-sm-12">
                                            <div class="categoryDiv">
                                                <p class="categoryHeader">{{$row1->category}}<label class="selectOnlyCategory"><input data-val="{{$key}}" class="selectOnlyCategory" name="selectOnlyCategory" type="checkbox">select all {{$row1->category}}</label></p>
                                            </div>
                                            @php $permissions = Helper::getPermissionByCategory($row1->category); @endphp
                                            @foreach($permissions as $row)
                                                <div class="col-lg-4 col-md-3 col-sm-3">
                                                    <label>{{ Form::checkbox('permission[]', $row->id, in_array($row->id, $rolePermissions) ? true : false, array('class' => 'name minimal mr-2 all '.$key.'')) }}
                                                        {{ $row->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                    {{-- <br/>
                                    @foreach($permission as $value)
                                        <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                                        {{ $value->name }}</label>
                                    <br/>
                                    @endforeach --}}
                                    @if ($errors->has('permission'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('permission') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="box" style="border-top:0">
                            <div class="box-footer">
                                <button type="submit" id="createBtn" class="btn btn-info pull-right" style="margin-left: 20px;">Submit</button>
                                <button type="button" id="cancelBtn" class="btn btn-default pull-right">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /.col -->
        </div>

        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
@endsection
@section('css')
<style>
    .permission_section label{
        font-weight:500;
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
            window.location.href = "{{route('roles.index')}}";
        });
    
        $(document.body).on('click', "#createBtn", function(){
            if ($("#dataForm").length){
                $("#dataForm").validate({
                  errorElement: 'span',
                          errorClass: 'text-red',
                          ignore: [],
                          rules: {
                            "name":{
                                required:true,
                            },
                        },
                        messages: {
                            "name":{
                                required:"Please enter role name.",
                            },
    
                          },
                          errorPlacement: function(error, element) {
                            error.insertAfter(element.closest(".form-control"));
                        },
                  });
            }
        });
    </script>
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
