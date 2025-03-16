<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Мое приложение</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="d-flex h-100">
    <div id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white bg-dark h-100">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-md-none me-3 sidebar-toggle">
                <i class="bi bi-x-lg"></i>
            </button>
            <a href="/" class="d-flex align-items-center mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-4">Print DB</span>
            </a>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            @if(auth()->check())
                <li class="nav-item">
                    <a href="{{ route('print.tasks.index') }}" class="nav-link {{ request()->is('print/tasks') ? 'active' : 'text-white' }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        {{ __('menu.tasks') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('print.parts.index') }}" class="nav-link {{ request()->is('print/parts') ? 'active' : 'text-white' }}">
                        <i class="bi bi-table me-2"></i>
                        {{ __('menu.parts') }}
                    </a>
                </li>
                @if(auth()->user()->hasRole(\App\Enums\UserRole::ADMIN))
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : 'text-white' }}">
                            <i class="bi bi-people me-2"></i>
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
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>Sign out
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <main class="col px-md-4">
        <div class="d-md-none p-3">
            <button class="btn btn-dark sidebar-toggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
        @yield('content')
    </main>
</div>
<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
