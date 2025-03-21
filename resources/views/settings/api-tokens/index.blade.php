@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">{{ __('settings.api_tokens.title') }}</h1>
            </div>
            <div>
                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('common.buttons.back') }}
                </a>
                <button type="button" class="btn btn-primary" id="createToken">
                    <i class="bi bi-plus-lg me-2"></i>{{ __('settings.api_tokens.create') }}
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('settings.api_tokens.token') }}</th>
                                <th>{{ __('settings.api_tokens.created') }}</th>
                                <th>{{ __('settings.api_tokens.last_used') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="tokensTable">
                            @foreach($tokens as $token)
                                <tr id="token-{{ $token->id }}">
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control copy-input" readonly
                                                   value="{{ $token->token }}">
                                            <button class="btn btn-outline-secondary copy-btn" type="button">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $token->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $token->last_used_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('settings.api-tokens.destroy', $token) }}"
                                              method="POST"
                                              class="d-inline delete-token-form"
                                              data-token-id="{{ $token->id }}"
                                              data-confirm-title="{{ __('settings.api_tokens.delete_confirm_title') }}"
                                              data-confirm-text="{{ __('settings.api_tokens.delete_confirm_text') }}"
                                              data-confirm-button="{{ __('common.buttons.delete') }}"
                                              data-cancel-button="{{ __('common.buttons.cancel') }}">
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('createToken').addEventListener('click', function() {
    fetch('{{ route('settings.api-tokens.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
});

document.querySelectorAll('.delete-token-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const tokenId = this.dataset.tokenId;

        Swal.fire({
            title: this.dataset.confirmTitle,
            text: this.dataset.confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: this.dataset.confirmButton,
            cancelButtonText: this.dataset.cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`token-${tokenId}`).remove();
                    }
                });
            }
        });
    });
});
</script>@endpush
