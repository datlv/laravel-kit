<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('website.name_long'))</title>
    <link href="{{ mix('css/backend.css') }}" rel="stylesheet">
    <script src="/js/pace.min.js" type="text/javascript"></script>
    <script>window.Laravel = {!!json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    @yield('head')
</head>
<body class="blank-layout gray-bg">
<div class="middle-box text-center loginscreen">
    <div class="logo">
        <div class="app-name"> {{setting('website.name_long') }}</div>
    </div>
    <h3 class="text-danger text-center">
        @section('heading')
            {!! trans('backend.cpanel')  !!}
        @show
    </h3>
</div>
<div class="container text-center">
    @if ($message = Session::get('message'))
        <div class="alert alert-{{ $message['type'] }}">
            {!! $message['content'] !!}
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <strong>{{ trans('errors.whoops') }}</strong><br>{{ $errors->has('msg') ? $errors->first('msg') : trans('errors.input') }}
        </div>
    @endif
</div>
<div id="app" class="@yield('classes', 'middle-box text-center loginscreen')">
    @yield('content')

    <p class="m-t text-center">
        <small>{{setting('website.name_long') }} &copy; {{date('Y')}}</small>
    </p>
</div>
<script src="{{ mix('js/backend.js') }}"></script>
@stack('scripts')
</body>
</html>
