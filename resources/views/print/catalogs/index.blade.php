@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Дерево каталогов (левая часть) -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>{{ __('part.catalog.titles') }}</h5>
                        <button type="button" class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#catalogModal"
                                data-action="{{ route('print.catalogs.store') }}"
                                data-create-route="{{ route('print.catalogs.create') }}">
                            <i class="bi bi-plus-lg"></i> {{ __('part.catalog.add') }}
                        </button>
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group catalog-tree border-0">
                            @include('print.catalogs.tree-items', ['catalogs' => $rootCatalogs])
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Список моделей (правая часть) -->
            <div class="col-md-8">
                <div class="card parts-panel">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 id="current-catalog-name">{{ __('part.catalog.select_catalog') }}</h5>
                        <div id="catalog-actions" class="d-none">
                            <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#partModal"
                                    id="add-part-button"
                                    data-action=""
                                    data-create-route="">
                                <i class="bi bi-plus-lg"></i> {{ __('part.add') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="parts-container">
                        <div class="text-center text-muted p-3">
                            {{ __('part.catalog.select_catalog_prompt') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальные окна без изменений -->
    <div class="modal fade" id="catalogModal" tabindex="-1" data-type="formModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('part.catalog.form_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initCatalogTree(); // Сначала инициализируем дерево каталогов
            checkUrlForCatalogId();

            // Обработчик клика по каталогу
            document.querySelectorAll('.catalog-item-link').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const catalogId = this.dataset.catalogId;
                    const catalogName = this.dataset.catalogName;
                    loadCatalogParts(catalogId, catalogName);

                    // Выделение активного каталога
                    document.querySelectorAll('.catalog-item-link').forEach(el => {
                        el.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Изменяем URL без перезагрузки страницы, без хэша
                    const newUrl = updateUrlParameter(window.location.href, 'catalog', catalogId);
                    history.pushState({catalogId}, '', newUrl);
                });
            });

            // Добавляем обработчик для кнопок сворачивания/разворачивания
            document.querySelectorAll('.toggle-catalog').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const catalogId = this.dataset.id;
                    const childContainer = document.querySelector(`.catalog-children[data-parent-id="${catalogId}"]`);
                    const icon = this.querySelector('.toggle-icon');

                    if (childContainer) {
                        // Переключаем видимость
                        if (childContainer.classList.contains('d-none')) {
                            // Разворачиваем
                            childContainer.classList.remove('d-none');
                            if (icon) {
                                icon.classList.remove('bi-chevron-right');
                                icon.classList.add('bi-chevron-down');
                            }
                            Cookies.set(`catalog_expanded_${catalogId}`, '1', {expires: 30});
                        } else {
                            // Сворачиваем
                            childContainer.classList.add('d-none');
                            if (icon) {
                                icon.classList.remove('bi-chevron-down');
                                icon.classList.add('bi-chevron-right');
                            }
                            Cookies.remove(`catalog_expanded_${catalogId}`);
                        }
                    }
                });
            });

            // Проверяем URL на наличие ID каталога после инициализации
            setTimeout(checkUrlForCatalogId, 100);

            // Функция обновления параметра в URL без добавления #
            function updateUrlParameter(url, param, value) {
                const urlObj = new URL(url, window.location.origin);
                urlObj.searchParams.set(param, value);
                return urlObj.pathname + urlObj.search;
            }

            // Улучшенная функция проверки URL при загрузке страницы
            function checkUrlForCatalogId() {
                const urlParams = new URLSearchParams(window.location.search);
                const catalogId = urlParams.get('catalog');

                if (catalogId) {
                    // Находим и кликаем по ссылке каталога с нужным ID
                    const catalogLink = document.querySelector(`.catalog-item-link[data-catalog-id="${ catalogId }"]`);
                    if (catalogLink) {
                        // Раскрываем родительские элементы, если они свёрнуты
                        expandParentCatalogs(catalogLink);

                        // Имитируем клик по ссылке каталога, но без изменения URL
                        const catalogId = catalogLink.dataset.catalogId;
                        const catalogName = catalogLink.dataset.catalogName;

                        // Выделение активного каталога
                        document.querySelectorAll('.catalog-item-link').forEach(el => {
                            el.classList.remove('active');
                        });
                        catalogLink.classList.add('active');

                        // Загружаем содержимое
                        loadCatalogParts(catalogId, catalogName);
                    }
                }
            }

            // Улучшенная функция для раскрытия родительских каталогов
            function expandParentCatalogs(catalogLink) {
                const parents = getParentCatalogs(catalogLink);
                parents.forEach(parentId => {
                    const toggleButton = document.querySelector(`.toggle-catalog[data-id="${ parentId }"]`);
                    const childContainer = document.querySelector(`.catalog-children[data-parent-id="${ parentId }"]`);

                    if (toggleButton && childContainer && childContainer.classList.contains('d-none')) {
                        // Раскрываем родительский каталог без добавления в историю браузера
                        childContainer.classList.remove('d-none');

                        const icon = toggleButton.querySelector('.toggle-icon');
                        if (icon) {
                            icon.classList.remove('bi-chevron-right');
                            icon.classList.add('bi-chevron-down');
                        }

                        // Сохраняем состояние в куках
                        Cookies.set(`catalog_expanded_${ parentId }`, '1', {expires: 30});
                    }
                });
            }

            // Функция для получения ID всех родительских каталогов
            function getParentCatalogs(catalogLink) {
                const parents = [];
                let currentElement = catalogLink.closest('.catalog-item');

                while (currentElement) {
                    // Ищем родительский UL с классом catalog-children
                    const parentUl = currentElement.closest('.catalog-children');
                    if (!parentUl) break;

                    const parentId = parentUl.dataset.parentId;
                    if (parentId) {
                        parents.push(parentId);
                    }

                    // Переходим на уровень выше
                    currentElement = parentUl.closest('.catalog-item');
                }

                return parents;
            }

            // Функция загрузки деталей каталога
            function loadCatalogParts(catalogId, catalogName) {
                fetch(`/print/catalogs/${catalogId}/parts`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('parts-container').innerHTML = html;
                        document.getElementById('current-catalog-name').textContent = catalogName;

                        // Настройка кнопки добавления модели
                        const addButton = document.getElementById('add-part-button');
                        addButton.dataset.action = `/print/parts`;
                        addButton.dataset.createRoute = `/print/parts/${catalogId}/create`;

                        // Показываем действия с каталогом
                        document.getElementById('catalog-actions').classList.remove('d-none');
                    });
            }

            // Обработчик изменения истории браузера (для кнопки "назад")
            window.addEventListener('popstate', function(event) {
                if (event.state && event.state.catalogId) {
                    const catalogLink = document.querySelector(`.catalog-item-link[data-catalog-id="${event.state.catalogId}"]`);
                    if (catalogLink) {
                        catalogLink.click();
                    }
                }
            });
        });
    </script>
@endpush
