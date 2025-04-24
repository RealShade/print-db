@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('admin.user.card') }}</h1>
    <div class="card">
        <div class="card-header">
            {{ __('admin.user.user') }} #{{ $user->id }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('admin.user.name') }}:</strong> {{ $user->name }}</p>
            <p><strong>{{ __('admin.user.email') }}:</strong> {{ $user->email }}</p>
            <p><strong>{{ __('admin.user.status') }}:</strong>
                <span class="badge badge-{{ strtolower($user->status->name) }}">
                    {{ $user->status->label() }}
                </span>
            </p>
            <p><strong>{{ __('admin.user.registration_date') }}:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('common.buttons.back') }}</a>
                @if($user->status === \App\Enums\UserStatus::NEW)
                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">{{ __('common.buttons.confirm') }}</button>
                    </form>
                @endif
                @if($user->status === \App\Enums\UserStatus::ACTIVE)
                    <form action="{{ route('admin.users.block', $user) }}"
                          method="POST"
                          class="d-inline confirm-block"
                          data-confirm-title="{{ __('admin.buttons.block') }}?"
                          data-confirm-text="{{ __('admin.user.confirm_block') }}"
                          data-confirm-button="{{ __('common.buttons.confirm') }}"
                          data-cancel-button="{{ __('common.buttons.cancel') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">{{ __('admin.buttons.block') }}</button>
                    </form>
                @elseif($user->status === \App\Enums\UserStatus::BLOCKED)
                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">{{ __('admin.buttons.unblock') }}</button>
                    </form>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-danger"
                    data-transport="ajax"
                    data-action="{{ route('admin.users.destroy', $user) }}"
                    data-method="DELETE"
                    data-confirm="true"
                    data-confirm-title="{{ __('common.buttons.delete') }}?"
                    data-confirm-text="{{ __('admin.user.confirm_delete') }}"
                    data-confirm-button="{{ __('common.buttons.confirm') }}"
                    data-cancel-button="{{ __('common.buttons.cancel') }}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>
@endsection
