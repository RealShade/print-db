<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мое приложение</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        html, body {
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="d-flex h-100">
        <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark h-100" style="width: 280px;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
                <span class="fs-4">Sidebar</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                @if(auth()->check())
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('print*') ? 'active' : 'text-white' }}" aria-current="page">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg>
                            {{ __('menu.print') }}
                        </a>
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('print/tasks') ? 'active' : 'text-white' }}" href="{{ route('print.tasks') }}">
                                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"/></svg>
                                    {{ __('menu.tasks') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('print/parts') ? 'active' : 'text-white' }}" href="{{ route('print.parts') }}">
                                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                                    {{ __('menu.parts') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if(auth()->user()->hasRole(\App\Enums\UserRole::ADMIN))
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : 'text-white' }}">
                                <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                                {{ __('menu.users') }}
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ auth()->user()->gravatar_url }}" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong>{{ auth()->user()->name }}</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                    </li>
                </ul>
            </div>
        </div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
