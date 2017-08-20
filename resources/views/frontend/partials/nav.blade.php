<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        </div>
        <div id="top-nav" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li {!! (Request::is('home') ? 'class="active"' : '') !!}>
                    <a href="{{ route('home') }}">
                        <i class="glyphicon glyphicon-home"></i> 首页
                    </a>
                </li>
                <li {!! (Request::is('order') ? 'class="active"' : '') !!}>
                    <a href="{{ route('order.index') }}">
                        <i class="glyphicon glyphicon-cutlery"></i> 饿单
                    </a>
                </li>

                <li {!! (Request::is('attendance') ? 'class="active"' : '') !!}>
                    <a href="{{ route('attendance.index') }}">
                        <i class="glyphicon glyphicon-hand-up"></i> 考勤
                    </a>
                </li>

                <li {!! (Request::is('orders') ? 'class="active"' : '') !!}>
                    <a href="/order">
                        <i class="glyphicon glyphicon-stats"></i> 统计
                    </a>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ route('login') }}"><i class="glyphicon glyphicon-log-in"></i> 登录</a></li>
                    <li><a href="{{ route('register') }}"><i class="glyphicon glyphicon-heart"></i> 注册</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="glyphicon glyphicon-user"></i> {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <i class="glyphicon glyphicon-log-out"></i> 退出
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
