@section('title') Users | @endsection
@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">    
        <h1>Users</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Users</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            @if(Auth::user()->can('user-create'))
              <div class="col-sm-2 pull-right" style="padding-bottom: 10px;"> 
                  <a href="{{route('users.create')}}"><button type="button" class="btn btn-block btn-primary">New User</button></a>
              </div>
            @endif
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                      {!! $html->table(['class' => 'table table-bordered','id'=>'UserDataTable']) !!}
                        
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
@section('script')
{!! $html->scripts() !!}
@if(Session::has('message'))
    <script>
    $(function() {
      toastr.{{ Session::get('alert-class') }}('{{ Session::get('message') }}');
    });
    </script>
  @endif

  @if(Session::has('errors'))
    <script>
    $(function() {
      $('#myModal').modal('show');
    });
    </script>
  @endif
<script>

  var SITE_URL = "<?php echo URL::to('/'); ?>";
// change user Status
  $(document.body).on('click', '.actStatus' ,function(event){
    var row = this.id;
    var dbid = $(this).attr('data-sid');
    bootbox.confirm({
      message: "Are you sure you want to change user status ?",
      buttons: {'cancel': { label: 'No',className: 'btn-danger'},
      'confirm': { label: 'Yes',className: 'btn-success'}
    },
    callback: function(result){  
      if (result){
        $.ajax({
          type :'POST',
          data : {id:dbid, _token:'{{ csrf_token() }}'},
          url  : 'users/status-change',
          success  : function(response) {
            if (response == 'Active') {
                $('#'+row+'').text('Active').removeClass('text-danger').addClass('text-green');
            }
            else if(response == 'Deactive') {
                $('#'+row+'').text('Deactive').removeClass('text-green').addClass('text-danger');
            }
            else if(response == 'error') {
              bootbox.alert('Something Went to Wrong');
            }
          }
         });
        }
      }
    });
  });

  function deleteConfirm(id){
    if(id==1){
       bootbox.alert("You can't delete Super Admin");
    }
    else{
    bootbox.confirm({
      message: "Are you sure you want to delete ?",
      buttons: {'cancel': {label: 'No',className: 'btn-danger'},
                'confirm': {label: 'Yes',className: 'btn-success'}
      },
      callback: function(result){
        if (result){
          $.ajax({            
            url: SITE_URL + '/admin/users/'+id,
            type: "DELETE",
            cache: false,
            data:{ _token:'{{ csrf_token() }}'},
            success: function (data, textStatus, xhr) {
              if(data== true && textStatus=='success' && xhr.status=='200')
              {
                  toastr.warning('User Deleted !!');
                  $('#UserDataTable').DataTable().ajax.reload(null, false);
              }
              else {  toastr.error(data); }
            }
          });
        }
      }
    });
  }
  }  
</script>
@endsection
