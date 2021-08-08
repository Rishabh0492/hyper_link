@section('title')
Role Users |
@endsection
@extends('admin.layouts.adminMaster')
@section('content')
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Role Users</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Role Users</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-sm-2 pull-right" style="padding-bottom: 10px;">
                <a href="{{url('admin/addNewroleUser')}}"><button type="button" class="btn btn-block btn-primary">New Role User</button></a>
            </div>

            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
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
@section('css')
<style>
    .alert{
        padding: 6px !important;
    }
    .actStatus{
        cursor: pointer;
    }
</style>
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
    var SITE_URL = "<?php echo URL::to('/'); ?>";

    $(function() {
    $('#example1').DataTable({
            //stateSave: true,
            "scrollX": false,
            processing: true,
            serverSide: true,
            //searchDelay: 1000,
            ajax: SITE_URL + '/admin/ajaxroleUsers',
            columns: [
            {data: 'id', name: 'id'},
            {data: 'email', name: 'email'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "order": [[ 0, "desc" ]]
        });

    });
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
                      "first_name":{
                          required:true,
                          minlength: 2,
                          maxlength: 20
                      },
                      "last_name":{
                          required:true,
                          minlength: 2,
                          maxlength: 20
                      },
                      "email":{
                        required:true,
                        email:true,
                          remote: {
                              url: SITE_URL + '/check-email-exsist',
                              type: "get"
                          }
                      },
                      "user_mobile":{
                        required:true,
                          number:true,
                          minlength:10,
                          maxlength:15,
                          remote: {
                              url: SITE_URL + '/check-number-exsist',
                              type: "get"
                          }
                      },
                      "dob":{
                        required:true,
                      },
                      "confirm_password":{
                        required:true,
                        equalTo:'#password',
                      },
                      "password":{
                        required:true,
                        minlength: 6,
                        maxlength: 20
                      },
                      "country":{
                        required:true,
                        minlength: 2,
                        maxlength: 20
                      },
                      "state":{
                        required:true,
                        minlength: 2,
                        maxlength: 20
                      },
                      "city":{
                        required:true,
                        minlength: 2,
                        maxlength: 20
                      },
                  },
                  messages: {
                      "email":{
                          required:"@lang('messages.registrationpage.reqemail')",
                          remote:"@lang('messages.registrationpage.remoteEmail')",
                      },
                        "first_name":{
                          required:"@lang('messages.registrationpage.reqfirstname')",
                      },
                        "last_name":{
                          required:"@lang('messages.registrationpage.reqLastname')",
                      },
                        "user_mobile":{
                          required:"@lang('messages.registrationpage.reqMobileno')",
                          minlength: "@lang('messages.registrationpage.minmobile')",
                            maxlength: "@lang('messages.registrationpage.maxmobile')",
                            remote:"@lang('messages.registrationpage.remoteMobile')",
                      },
                      "dob":{
                        required:"@lang('messages.registrationpage.reqDob')",
                      },
                        "confirm_password":{
                          required:"@lang('messages.registrationpage.reqConPassword')",
                          equalTo: "@lang('messages.registrationpage.reqEqualConPass')",
                      },
                      "password":{
                          required:"@lang('messages.registrationpage.reqPass')",
                      },
                      "country":{
                        required:"@lang('messages.registrationpage.reqCountry')",
                      },
                      "state":{
                        required:"@lang('messages.registrationpage.reqState')",
                      },
                      "city":{
                        required:"@lang('messages.registrationpage.reqCity')",
                      },
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element.closest(".form-control"));
                    },
            });
        }
    });

    $(document.body).on('click', '.actStatus' ,function(event){
        var row = this.id;
        var dbid = $(this).attr('data-sid');

        bootbox.confirm({
        message: "Are you sure you want to change user status ?",
            buttons: {
            'cancel': {
                label: 'No',
                className: 'btn-danger'
            },
                'confirm': {
                label: 'Yes',
                        className: 'btn-success'
                }
            },
            callback: function(result){
                if (result){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type :'POST',
                        data : {id:dbid},
                        url  : 'users/status-change',
                        success  : function(response) {

                            if (response == 'Active') {
                                $('#'+row+'').text('Active');
                                $('#'+row+'').removeClass('text-danger');
                                $('#'+row+'').addClass('text-green');
                            }
                            else if(response == 'Deactive') {
                                $('#'+row+'').text('Deactive');
                                $('#'+row+'').removeClass('text-green');
                                $('#'+row+'').addClass('text-danger');
                            }
                        }
                    });
                }
            }
        });
    });

    function deleteConfirm(id){
        bootbox.confirm({
        message: "Are you sure you want to delete ?",
            buttons: {
            'cancel': {
                label: 'No',
                className: 'btn-danger'
            },
                'confirm': {
                label: 'Yes',
                        className: 'btn-success'
                }
            },
            callback: function(result){
                if (result){
                    $.ajax({
                        url: SITE_URL + '/admin/users/delete/'+id,
                        success: function (data) {
                            toastr.warning('User Deleted !!');
                            $('#example1').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            }
        })
    }
</script>
@endsection
