@section('title')
Add New User |
@endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('/resources/assets/admin/plugins/lightbox2-master/dist/css/lightbox.css')}}">
@endsection
@extends('admin.layouts.adminMaster')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add New Group</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Add New Group</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <!-- Horizontal Form -->
                <div class="box">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" id="userForm" role="form" action="{{url('admin/groups/new')}}" method="post" enctype="multipart/form-data" >
                        <div class="col-sm-8">
                            {{ csrf_field() }}
                            <div class="box-body">

                                <div class="form-group {{ $errors->has('group_name') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="group_name">Group Name <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                      <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Group Name" value="{{old('group_name')}}"/>
                                      @if ($errors->has('group_name'))
                                      <span class="help-block alert alert-danger">
                                          <strong>{{ $errors->first('group_name') }}</strong>
                                      </span>
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('group_description') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="group_description">Group Description <span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <textarea id="group_description" name="group_description" class="form-control" placeholder="Group Description"></textarea>
                                        @if ($errors->has('group_description'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('group_description') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('user_id') ? ' has-error' : '' }}">
                                    <label  class="col-sm-4 control-label" for="country">Group Users<span class="colorRed"> *</span></label>
                                    <div class="col-sm-8">
                                        <select id="user_id" name="user_id[]" multiple class="form-control">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->first_name}}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('user_id'))
                                        <span class="help-block alert alert-danger">
                                            <strong>{{ $errors->first('user_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <span class="help-block"> <span class="colorRed"> *</span> mentioned fields are mandatory.</span>
                                    <button type="button" class="btn btn-default"  id="cancelBtn">Close</button>
                                    <button type="submit" id="createBtn" class="btn btn-info pull-right">Create</button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </form>
                    <div class="clearfix"></div>
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
@section('script')
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
$(document).ajaxStart(function() { Pace.restart(); });
 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var SITE_URL = "<?php echo URL::to('/'); ?>";
$.validator.addMethod("email", function(value, element) {
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
    }, "Please enter valid email.");
    $(document.body).on('click', "#createBtn", function(){
        if ($("#userForm").length){
            $("#userForm").validate({
            errorElement: 'span',
                    errorClass: 'text-red',
                    ignore: [],
                    rules: {
                      "group_name":{
                          required:true
                      },
                      "group_description":{
                          required:true,
                      },
                      "user_id":{
                        required:true,
                      },
                  },
                  messages: {
                        "first_name":{
                          required:"Group Name field is Required",
                      },
                        "group_description":{
                          required:"Group Description field is Required",
                      },
                      "user_id":{
                        required:"Group User field is Required",
                      },
                    },
                    errorPlacement: function(error, element) {
                        if(element.is('select')){
                            error.appendTo(element.closest("div"));
                        }else{
                            error.insertAfter(element.closest(".form-control"));
                        }
                    },
            });
        }
    });

$("#cancelBtn").click(function () {
    window.location.href = "{{url('admin/users')}}";
});
</script>
@endsection
