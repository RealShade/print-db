@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ __('part.title') }}</h1>
            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#partModal"
                    data-action="{{ route('print.parts.store') }}"
                    data-create-route="{{ route('print.parts.create') }}">
                <i class="bi bi-plus-lg"></i> {{ __('part.add') }}
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('part.version') }}</th>
                            <th>{{ __('part.version_date') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($parts as $part)
                            <tr>
                                <td class="text-end">{{ $part->id }}</td>
                                <td>{{ $part->name }}</td>
                                <td>{{ $part->version }}</td>
                                <td>{{ $part->version_date?->format('d.m.Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#partModal"
                                            data-action="{{ route('print.parts.update', $part) }}"
                                            data-edit-route="{{ route('print.parts.edit', $part) }}"
                                            data-method="PUT"
                                            data-id="{{ $part->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $parts->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="partModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('part.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
