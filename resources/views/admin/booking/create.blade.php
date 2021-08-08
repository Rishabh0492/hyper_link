@extends('admin.layouts.app')
@section('title') Create Booking |@endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Booking Create</h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Booking Create</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <form class="" id="dataForm" role="form" action="{{route('booking.store')}}" method="post" enctype="multipart/form-data" >
                {{ csrf_field() }}
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Booking</h3>
                        </div>
                    
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">Select User <span class="colorRed"> *</span></label>
                            <div class=""> 
                                <select name="name" id="name" class="form-control">
                                @foreach ($users as $role)
                                <option value="{{ $role->id }}"
                                    @if(isset($role_id) && $role->id == $role_id) selected @endif
                                    >{{$role->first_name.' '.$role->last_name }}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('first_name'))
                                <span class="help-block alert alert-danger">
                                <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            </div>
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">Select Booking Date <span class="colorRed"> *</span></label>
                            <div class=""> 
                                <input type="date" id="travell_date" name="travell_date" class="form-control">
                                @if ($errors->has('travell_date'))
                                <span class="help-block alert alert-danger">
                                <strong>{{ $errors->first('travell_date') }}</strong>
                                </span>
                                @endif
                            </div>
                            </div>
                           
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">No of Tickets<span class="colorRed"> *</span></label>
                                <Input class="form-control" type="text" id="no_of_guest" name="no_of_guest">
                            </div>
                            </div>
                            
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">Select Airline <span class="colorRed"> *</span></label>
                            <div class=""> 
                                <select name="airline" id="airline" class="form-control">
                                @foreach ($airline as $val)
                                <option value="{{ $val->id }}">{{$val->name}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('airline'))
                                <span class="help-block alert alert-danger">
                                <strong>{{ $errors->first('airline') }}</strong>
                                </span>
                                @endif
                            </div>
                            </div>
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">Base Fee <span class="colorRed"> *</span></label>
                                <Input class="form-control" type="text" id="fee" name="fee" >
                            </div>
                            </div>
                            <div class="box-body">
                            <div class="form-group">
                            <label  class=" control-label" for="geo_hub_name">Total Fair <span class="colorRed"> *</span></label>
                                <Input class="form-control" type="text" id="total_fair" disabled name="total_fair" value="">
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
@section('script')
<script type="text/javascript">
    $(document).ready(function (){
            $('select[name="airline"]').on('change',function(){
               var airlineID = $(this).val();
               if(airlineID){
                  $.ajax({
                     url : 'getfair/' +airlineID,
                     type : "GET",
                     dataType : "json",
                     success:function(data){
                        var endDate = $('#travell_date').val();
                        if(!endDate){
                          alert('Please Select Travell Date.');
                          return false;  
                        }
                        var no_of_ticket = $('#no_of_guest').val();
                        var total_fare = data*no_of_ticket; 
                       $('#fee').val(total_fare);
                       /**print curent date  **/
                       var d = new Date();
                        var month = d.getMonth()+1;
                        var day = d.getDate();
                        var curentDate = d.getFullYear() + '/' +
                            ((''+month).length<2 ? '0' : '') + month + '/' +
                            ((''+day).length<2 ? '0' : '') + day;
                            var startDay = new Date(curentDate);  
                            var endDate = new Date(endDate);   
                        // Determine the time difference between two dates     
                            var millisBetween = startDay.getTime() - endDate.getTime();  
                        // Determine the number of days between two dates  
                            var days = millisBetween / (1000 * 3600 * 24);
                            var diffDay = Math.round(Math.abs(days)); 
                            if(diffDay>30 && diffDay<45){
                               var  total_fare = total_fare +total_fare*(3/10);
                                $('#total_fair').val(total_fare);
                            }
                            if(diffDay < 30 && diffDay>15){
                               var  total_fare = total_fare +total_fare*(1/2);
                                $('#total_fair').val(total_fare);
                                retun;
                            }else if(diffDay > 5 && diffDay<15){
                               var  total_fare = total_fare +total_fare*(4/5);
                                $('#total_fair').val(total_fare);
                                retun;
                            }else if(diffDay<5){
                               var  total_fare = total_fare + total_fare;
                                $('#total_fair').val(total_fare);
                                retun;
                            }else{
                                var total_fare = total_fare;
                                $('#total_fair').val(total_fare);
                            }
                     }
                  });
               }
            });
    });
    </script>
@endsection
