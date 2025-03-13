@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('auth.registration_success_title') }}</div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p>{{ __('auth.registration_pending') }}</p>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                {{ __('auth.back_to_login') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
