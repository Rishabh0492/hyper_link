@section('title')
    Chat |
@endsection
@extends('layouts.app')

@section('content')
<section class="middle_sec with_out_innerbanner transaction chat_page">
	<div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12 col-xss-12">
                <div class="box box-primary">
                    <div class="box-body chat_list">
                        <ul class="chater_list">
                            @if(!$users->isEmpty())
                                @foreach($users as $user)
                                    @if($user->user_id1 == auth::user()->id)
                                        <li class="item" data-id="{{ $user->id }}">
                                            <div class="product-img">
                                                <img class="direct-chat-img" src="" alt="Image">
                                            </div>
                                            <div class="product-info">
                                                <a href="{{route('chat', [$user['userOneData']->id])}}" class="product-title active">
                                                    <b>
                                                        {{ $user['userOneData']->first_name }}
                                                        <span>
                                                            @if($user->message)
                                                                - {{ Helper::humanTiming($user->updated_at)}}
                                                            @endif
                                                        </span>
                                                    </b>

                                                    <br>
                                                    <span class="product-description">
                                                        {{-- <i class="fa fa-circle pull-right read-message notification_detsil_count" id="chatIco_{{$user->id}}" style="display:none;" aria-hidden="true"></i> --}}
                                                        @if($user->message)
                                                            {!!$user->message!!}
                                                        @else
                                                            Document
                                                        @endif
                                                    </span>

                                                </a>
                                            </div>
                                            <span class="read-message notification_detsil_count notifications_icon" id="chatIco_{{$user->id}}" style="display:none;"></span>
                                            {{-- @if($user->user_id1_read == '1')
                                                <span class="notifications_icon notification_detsil_count" >{{$user->user_id1_unread_count}}</span>
                                            @endif --}}
                                        </li>
                                    @elseif($user->user_id2 == auth::user()->id)
                                        <li class="item" data-id="{{ $user->id }}">
                                            <div class="product-img">
                                                <img class="direct-chat-img" src="" alt="Image">
                                            </div>
                                            <div class="product-info">
                                                <a href="{{route('chat', [$user['userTwoData']->id])}}" class="product-title active">
                                                    <b>
                                                        {{ $user['userTwoData']->first_name }}
                                                        <span>
                                                            @if($user->message)
                                                                - {{ Helper::humanTiming($user->updated_at)}}
                                                            @endif
                                                        </span>
                                                    </b><br>
                                                    <span class="product-description">
                                                        @if($user->message)
                                                        {!!$user->message!!}
                                                        @else
                                                        Document
                                                        @endif
                                                    </span>
                                                </a>
                                            </div>
                                            <span class="read-message notification_detsil_count notifications_icon" id="chatIco_{{$user->id}}" style="display:none;"></span>
                                            {{-- @if($user->user_id2_read == '1')
                                                <span class="notifications_icon notification_detsil_count" >{{$user->user_id2_unread_count}}</span>
                                            @endif --}}
                                        </li>
                                    @endif
                                @endforeach
                            @else
                                <li class="item">
                                    <h4 class="text-center" style="">No record found</h4>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
	</div>
</section>
@endsection

@section('css')
<style>
    .products-list .product-info {
        margin-top: 2px;
        font-size: 15px;
    }
    .ui.form {
        font-size: 1rem;
        position: relative;
    max-width: 100%;
    }
    .ui.form .field {
        clear: both;
        margin: 0 0 1em;
    }
    .ui.comments .reply.form textarea {
        font-size: 1em;
        height: 12em;
    }
    .ui.form textarea:not([rows]) {
        height: 12em;
        min-height: 5em;
        max-height: 5em;
        border-radius: 5px;
    }
    .ui.form input[type=checkbox], .ui.form textarea {
        vertical-align: top;
    }
    .ui.form textarea {
        margin: 0;
        -webkit-appearance: none;
        /* tap-highlight-color: rgba(255,255,255,0); */
        padding: .78571429em 1em;
        box-shadow: none;
    }

    .ui.button {
        cursor: pointer;
        padding: 5px 30px;
        color: #ffffff;
        background-color: #ef4426;
        border: none;
        border-radius: 2px;
        font-size: 15px;
    }
    .products-list{
        max-height: 550px;
    overflow-y: scroll;
    }
    .comment .content .text p{
        /* text-align: justify; */
    }
    /* Scrollbar styles */
    ::-webkit-scrollbar {
            width: 8px;
            height: 3px;
    }
    ::-webkit-scrollbar-track {
            /* box-shadow: inset 0 0 10px #31B0D5; */
            border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: #ef4426;
            /* box-shadow: inset 0 0 6px rgba(0,0,0,0.5);  */
    }
    .date-filter{
        /* background-color: #dd4b39 !important; */
        background-color: #ef4426 !important;
        border-color: #367fa9;
        color: #ffffff !important;
        border: 1px solid transparent;
        border-radius: 3px;
    }
    .date-filter::-webkit-input-placeholder {
    color: #ffffff;
    }
    .product-description img{
        height: 20px;
    }

</style>
@endsection


@section('script')
<script>
    var SITE_URL = "<?php echo URL::to('/'); ?>";
    notificationCount();
    setInterval(function()
    {
        notificationCount();
    }, 10000);
    function notificationCount(){
        var idArr = [];
        $(".item").each(function(){
            idArr.push($(this).attr("data-id"));
        });
        $.ajax({
            type:"GET",
            url:SITE_URL + "/getAllUnreadMessage",
            dataType: "json",
            success:function(data)
            {
                $('.read-message').hide();
                $.each(data,function( index, value ) {
                    if(jQuery.inArray(value.id, idArr) == -1){
                        $('#chatIco_'+value.id).show().html(value.unread_message_count);
                    }
                });
            }
        });
    };
</script>
@endsection
