@extends('layouts.guest')

@section('content')
    <div class="container form-container">
        <div class="text-center registration-success">
            <div class="alert alert-info">
                <h4 class="alert-heading">{{ __('auth.registration_success_title') }}</h4>
                <p style="text-decoration: line-through;">{{ __('auth.registration_pending') }}</p>
                <p>{{ __('auth.registration_free') }}</p>
            </div>
            <a href="{{ route('login') }}" class="text-decoration-none">
                {{ __('auth.back_to_login') }}
            </a>
        </div>
    </div>
@endsection
