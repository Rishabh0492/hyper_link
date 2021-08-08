<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ URL::asset('resources/assets/img/logo.png')}}" />

    <title>{{ config('app.name', 'Laravel') }}</title>


    @include('layouts.Css')
    @yield('css')
</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
    <script src="https://media.twiliocdn.com/sdk/js/chat/v3.3/twilio-chat.min.js"></script>
    @if(Request::is('chat/*'))
        <script type='text/javascript' src="{{URL::asset('/resources/assets/admin/plugins/moment/moment.min.js')}}"></script>
        <script src="https://www.gstatic.com/firebasejs/4.5.0/firebase.js"></script>
        <script>
            // Initialize Firebase
            var config = {
                apiKey: "{{ env('FIREBASE_APIKEY') }}",
                authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
                databaseURL: "{{ env('FIREBASE_DATABASEURL') }}",
                projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
                storageBucket: "{{ env('FIREBASE_STORAGEBUCKET') }}",
                messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDER_ID') }}"
            };
            firebase.initializeApp(config);

            const database = firebase.database();
            $(window).on("load",function(e){
                $( "#msg_back" ).click(function() {
                    $("#chating_box").hide();
                });
            });

        </script>
    @endif
    <script src="{{ URL::asset('public/js/app.js') }}"></script>
    @include('layouts.Js')
    @yield('script')
</body>
</html>
