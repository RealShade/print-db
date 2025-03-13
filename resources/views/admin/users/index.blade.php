@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('admin.user.list') }}</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('admin.user.id') }}</th>
                <th>{{ __('admin.user.status') }}</th>
                <th>{{ __('admin.user.email') }}</th>
                <th>{{ __('admin.user.registration_date') }}</th>
                <th>{{ __('admin.user.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($user->status->name) }}">
                        {{ $user->status->label() }}
                    </span>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                    <form action="{{ route('admin.users.block', $user) }}" method="POST" class="d-inline-block confirm-block" data-confirm-title="{{ __('admin.buttons.block') }}?" data-confirm-text="{{ __('admin.user.confirm_block') }}" data-confirm-button="{{ __('admin.buttons.confirm') }}" data-cancel-button="{{ __('admin.buttons.cancel') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="bi bi-lock"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline-block confirm-delete" data-confirm-title="{{ __('admin.buttons.delete') }}?" data-confirm-text="{{ __('admin.user.confirm_delete') }}" data-confirm-button="{{ __('admin.buttons.confirm') }}" data-cancel-button="{{ __('admin.buttons.cancel') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
