@section('title')
Users |
@endsection
@extends('admin.layouts.adminMaster')
@section('content')
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Groups</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Groups</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-sm-2 pull-right" style="padding-bottom: 10px;">
                <a href="{{route('addNewGroup')}}"><button type="button" class="btn btn-block btn-primary">New Group</button></a>
            </div>

            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Group Name</th>
                                    <th>Group Description</th>
                                    <th>Group Created By</th>
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
            ajax: SITE_URL + '/admin/ajaxGroups',
            columns: [
            {data: 'group_id', name: 'group_id'},
            {data: 'group_name', name: 'group_name'},
            {data: 'group_description', name: 'group_description'},
            {data: 'user_id', name: 'user_id'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "order": [[ 0, "desc" ]]
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
                        url: SITE_URL + '/admin/groups/delete/'+id,
                        success: function (data) {
                            toastr.warning('Group Deleted !!');
                            $('#example1').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            }
        })
    }
</script>
@endsection
