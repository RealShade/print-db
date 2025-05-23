<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>3D print organizer</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
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
                <span class="fs-4"><strong>Print DB</strong></span>
            </a>
            <div class="text-muted small">by <a href="https://t.me/realshade" target=_blank>realshade</a></div>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto menu">
            @if(auth()->check())
                <li class="nav-item">
                    <a href="{{ route('printers.index') }}" class="nav-link {{ request()->is('printers') ? 'active' : 'text-white' }}">
                        <img src="{{ asset('images/menu/printer.svg') }}" alt="Printers">
                        {{ __('menu.printers') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('print.tasks.index') }}" class="nav-link {{ request()->is('print/tasks') ? 'active' : 'text-white' }}">
                        <img src="{{ asset('images/menu/tasks.svg') }}" alt="Tasks">
                        {{ __('menu.tasks') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('print.catalogs.index') }}" class="nav-link {{ request()->is('print/parts') ? 'active' : 'text-white' }}">
                        <img src="{{ asset('images/menu/model.svg') }}" alt="Models">
                        {{ __('menu.parts') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center justify-content-between"
                       data-bs-toggle="collapse"
                       href="#filamentCollapse"
                       role="button"
                       aria-expanded="{{ request()->routeIs('filament.*') ? 'true' : 'false' }}"
                       aria-controls="filamentCollapse">
                        <span>
                            <img src="{{ asset('images/menu/filament.svg') }}" alt="Filament">
                            {{ __('menu.filament.title') }}
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('filament.*') ? 'show' : '' }}" id="filamentCollapse">
                        <ul class="nav flex-column ms-4">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('filament.spools.*') ? 'active' : 'text-white' }}" href="{{ route('filament.spools.index') }}">{{ __('menu.filament.reels') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('filament.filament.*') ? 'active' : 'text-white' }}" href="{{ route('filament.filament.index') }}">{{ __('menu.filament.filaments') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('filament.vendors.*') ? 'active' : 'text-white' }}" href="{{ route('filament.vendors.index') }}">{{ __('menu.filament.manufacturers') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('filament.types.*') ? 'active' : 'text-white' }}" href="{{ route('filament.types.index') }}">{{ __('menu.filament.types') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('filament.packaging.*') ? 'active' : 'text-white' }}" href="{{ route('filament.packaging.index') }}">{{ __('menu.filament.packaging') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.*') ? 'active' : 'text-white' }}">
                        <img src="{{ asset('images/menu/history.svg') }}" alt="History">
                        {{ __('printer.history.title') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tools.index') }}" class="nav-link {{ request()->routeIs('tools.*') ? 'active' : 'text-white' }}">
                        <img src="{{ asset('images/menu/tools.svg') }}" alt="Tools">
                        {{ __('tools.title') }}
                    </a>
                </li>
                @if(auth()->user()->hasRole(\App\Enums\UserRole::ADMIN))
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : 'text-white' }}">
                            <img src="{{ asset('images/menu/users.svg') }}" alt="Users">
                            {{ __('menu.users') }}
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center justify-content-between"
                       data-bs-toggle="collapse"
                       href="#helpCollapse"
                       role="button"
                       aria-expanded="{{ request()->routeIs('help.*') ? 'true' : 'false' }}"
                       aria-controls="helpCollapse">
                        <span>
                            <img src="{{ asset('images/menu/help.svg') }}" alt="Help">
                            {{ __('menu.help.title') }}
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('help.*') ? 'show' : '' }}" id="helpCollapse">
                        <ul class="nav flex-column ms-4">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('help.api.*') ? 'active' : 'text-white' }}" href="{{ route('help.api.index') }}">{{ __('menu.help.api') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ auth()->user()->gravatar_url }}" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>{{ auth()->user()->name }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i>{{ __('menu.settings') }}
                    </a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>{{ __('menu.profile') }}</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('menu.logout') }}
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
<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
