@foreach($catalogs as $catalog)
    <li class="list-group-item catalog-item">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($catalog->children->count() > 0)
                    <button class="btn btn-sm p-0 me-1 toggle-catalog" data-id="{{ $catalog->id }}">
                        <i class="bi bi-chevron-right toggle-icon"></i>
                    </button>
                @else
                    <span class="ps-3"></span>
                @endif
                <a href="#" class="catalog-item-link text-decoration-none"
                   data-catalog-id="{{ $catalog->id }}"
                   data-catalog-name="{{ $catalog->name }}">
                    {{ $catalog->name }}
                    <span class="badge bg-primary rounded-pill">{{ $catalog->parts->count() }}</span>
                </a>
            </div>
            <div class="catalog-actions btn-group" data-hover-target="card">
                <button class="btn btn-sm btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#catalogModal"
                        data-action="{{ route('print.catalogs.store') }}"
                        data-create-route="{{ route('print.catalogs.create.with.parent', $catalog) }}">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#catalogModal"
                        data-action="{{ route('print.catalogs.update', $catalog) }}"
                        data-edit-route="{{ route('print.catalogs.edit', $catalog) }}"
                        data-method="PUT"
                        data-id="{{ $catalog->id }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger"
                        data-transport="ajax"
                        data-action="{{ route('print.catalogs.destroy', $catalog) }}"
                        data-method="DELETE"
                        data-confirm="true"
                        data-confirm-title="{{ __('common.confirm.delete.title') }}"
                        data-confirm-text="{{ __('common.confirm.delete.text') }}?"
                        data-confirm-button="{{ __('common.buttons.confirm') }}"
                        data-cancel-button="{{ __('common.buttons.cancel') }}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <ul class="list-group catalog-children d-none" data-parent-id="{{ $catalog->id }}">
            @include('print.catalogs.tree-items', ['catalogs' => $catalog->children])
        </ul>
    </li>
@endforeach
