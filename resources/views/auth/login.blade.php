@extends('layouts.guest')

@section('content')
    <div class="container form-container">
        <div class="justify-content-center">
            <div class="card form-card">
                <form method="POST" action="{{ route('login') }}">
                    <div class="card-header">{{ __('auth.login') }}</div>
                    <div class="card-body">
                        @csrf
                        <div class="form-group">
                            <label for="email">{{ __('auth.email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ __('auth.invalid_credentials') }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('auth.password') }}</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                   required autocomplete="current-password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ __('auth.invalid_credentials') }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">{{ __('auth.login') }}</button>
                        <a href="{{ route('register') }}" class="text-decoration-none">{{ __('auth.register') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
