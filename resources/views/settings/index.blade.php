@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">{{ __('settings.title') }}</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('settings.api-tokens.index') }}" class="btn btn-primary">
                            <i class="bi bi-key me-2"></i>{{ __('settings.api_tokens.title') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
