<!doctype html>
<html lang="{!! App::getLocale() !!}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="Minh Bang">
    <link rel="shortcut icon" href="/mb-favicon.ico">
    <title>@yield('title', setting('website.name_short').' Administrator')</title>
    <link href="{!! mix('css/backend.css') !!}" rel="stylesheet">
    <script src="/js/pace.min.js"></script>
    <script> window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};</script>
    @stack('head')
</head>
<body class="layout-modal">
<div id="app" class="container-fluid">
    @yield('content')
</div>
<script type="text/javascript">
    (function (window) {
        window.settings = window.settings || {};
        window.settings.datetimepicker = {
            lang: '{{config("app.locale")}}',
            format: 'd/m/Y',
            timepicker: false,
            step: 15,
        };
        window.trans = window.trans || {
            ok: "{{trans('common.ok')}}",
            cancel: "{{trans('common.cancel')}}",
            close: "{{trans('common.close')}}",
        };
    })(window);
</script>
<script src="{!! mix('js/backend.js') !!}" type="text/javascript"></script>
@stack('scripts')
<script type="text/javascript">
    $(document).waitForImages(function () {
        window.$.fn.mbHelpers.updateModalHeight();
    });
    $(document).ready(function () {
        window.$.fn.mbHelpers.updateModalHeight();
    });
</script>
</body>
</html>