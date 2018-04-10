<!doctype html>
<html lang="{!! App::getLocale() !!}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Minh Bang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/mb-favicon.ico">
    <title>@yield('title', setting('website.name_short').' Administrator')</title>
    <link href="{!! mix('css/backend.css') !!}" rel="stylesheet">
    <script src="/js/pace.min.js" type="text/javascript"></script>
    <script> window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};</script>
    @yield('head')
</head>
<body class="@yield('skin')">
<div id="wrapper">
    <nav id="sidebar" class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            @section('sidebar')
              @if(app()->has('menu-manager'))
                  {!! app('menu-manager')->render(
                      'backend.sidebar', [
                          'header' => '<div class="logo"><div class="app-name">'.setting('website.name_short').'</div></div><div class="logo-element"><div class="logo-small"></div></div>'
                      ]
                  ) !!}
              @endif
            @show
        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top {!! $nav_bg or (empty($page_heading) ? 'white':'gray') !!}-bg"
                 role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i>
                    </a>
                    <div class="module-name">
                        @section('module-name')
                            {!! Html::twoPart(trans('backend.cpanel'), 'text-danger', false, ' ') !!}
                        @show
                    </div>
                </div>
                <ul class="nav navbar-nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span> @yield('user-name', user('name'))
                            <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('account.password')}}">
                                    <span class="fa fa-user-secret"></span> {{ trans('user::account.update_password') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{route('account.profile')}}">
                                    <span class="fa fa-list-alt"></span> {{ trans('user::account.profile') }}
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{route('auth.logout')}}">
                                    <span class="glyphicon glyphicon-off"></span> {{ trans('user::account.logout') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
        @if( !empty($page_heading) )
            <?php $has_btttons = ! empty($page_buttons); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-md-{{$has_btttons ? 6 : 12}}">
                    <h2>
                        {!! empty($page_icon) ? '': mb_icon_html($page_icon).' ' !!}{!! Html::twoPart($page_heading, 'text-warning', true, '|||') !!}
                    </h2>
                    @if( !empty($breadcrumbs) )
                        {!! Html::breadcrumb($breadcrumbs) !!}
                    @endif
                </div>
                @if($has_btttons)
                    <div class="col-md-6">
                        <div class="buttons">{!!Html::linkButtons($page_buttons)!!}</div>
                    </div>
                @endif
            </div>
        @endif
        <div id="app" class="wrapper wrapper-content">
            @yield('content')
        </div>
        <div class="footer">
            <div class="pull-right">
                &copy; {!! date('Y') !!} <a href="{!! URL::to('/') !!}">{!! setting('website.name_long') !!}</a>.
            </div>
            <div>
                Version {{config('app.version')}}  —  Laravel Framework {{app()->version()}} — PHP {{phpversion()}}
                <!--Thiết kế và Phát triển bởi <a href="http://minhbang.com">Minh Bằng</a>.-->
            </div>
        </div>
    </div>
</div>
@yield('mixins')
@if ($message = Session::get('message'))
    <script type="text/javascript">
        var message = {type: '{!! $message['type'] !!}', content: '{!! $message['content'] !!}'};
    </script>
@endif
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
</body>
</html>
