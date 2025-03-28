import Swal from 'sweetalert2';
import Cookies from 'js-cookie';

window.Swal = Swal;

document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const togglePassword = document.querySelector('.toggle-password');
    const password = document.querySelector('#password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    // Confirmation dialogs
    document.querySelectorAll('.confirm-block, .confirm-delete').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title            : form.dataset.confirmTitle,
                text             : form.dataset.confirmText,
                icon             : 'warning',
                showCancelButton : true,
                confirmButtonText: form.dataset.confirmButton,
                cancelButtonText : form.dataset.cancelButton
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Mobile menu
    const toggleButtons = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    toggleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });
    });

    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 &&
            !sidebar.contains(e.target) &&
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('show');
        }
    });

    // Clipboard handling
    document.addEventListener('click', function(e) {
        if (e.target.closest('.copy-btn')) {
            const btn = e.target.closest('.copy-btn');
            const input = btn.closest('.input-group').querySelector('.copy-input');
            input.select();
            document.execCommand('copy');

            const icon = btn.querySelector('i');
            icon.classList.remove('bi-clipboard');
            icon.classList.add('bi-clipboard-check');

            setTimeout(() => {
                icon.classList.remove('bi-clipboard-check');
                icon.classList.add('bi-clipboard');
            }, 2000);
        }
    });

    // Обработчик AJAX-запросов для кнопок
    document.addEventListener('click', function(e) {
        const button = e.target.closest('[data-transport="ajax"]');
        if (!button) {
            return;
        }

        e.preventDefault();

        const makeRequest = () => {
            const action = button.dataset.action;
            const method = button.dataset.method || 'POST';

            fetch(action, {
                method : method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type'    : 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw response;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    error.json().then(data => {
                        Swal.fire({
                            icon : 'error',
                            title: 'Помилка',
                            text : data.message || 'Виникла помилка при виконанні дії'
                        });
                    });
                });
        };

        if (button.dataset.confirm === 'true') {
            Swal.fire({
                title            : button.dataset.confirmTitle,
                text             : button.dataset.confirmText,
                icon             : 'warning',
                showCancelButton : true,
                confirmButtonText: button.dataset.confirmButton,
                cancelButtonText : button.dataset.cancelButton
            }).then((result) => {
                if (result.isConfirmed) {
                    makeRequest();
                }
            });
        } else {
            makeRequest();
        }
    });

    // Modal forms handling
    const initModalForm = (modal) => {
        let partsModal = null;
        let currentTaskButton = null;
        let partsModalElement = null;

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button) {
                currentTaskButton = button;
            }

            const action = currentTaskButton.dataset.action;
            const method = currentTaskButton.dataset.method || 'POST';
            const id = currentTaskButton.dataset.id;
            const formUrl = id
                ? currentTaskButton.dataset.editRoute
                : currentTaskButton.dataset.createRoute;

            fetch(formUrl)
                .then(response => response.text())
                .then(html => {
                    const modalBody = modal.querySelector('.modal-body');
                    modalBody.innerHTML = html;

                    const form = modalBody.querySelector('form');
                    form.action = action;
                    if (method === 'PUT') {
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'PUT';
                        form.appendChild(methodField);
                    }

                    // Инициализация механизма выбора деталей для задачи
                    if (modal.id === 'taskModal') {
                        const newPartsModalContent = modalBody.querySelector('#partsModal');
                        if (newPartsModalContent) {
                            // Очищаем предыдущее модальное окно
                            if (partsModalElement) {
                                partsModalElement.remove();
                            }

                            // Перемещаем новое модальное окно
                            partsModalElement = newPartsModalContent;
                            document.body.appendChild(partsModalElement);
                            partsModal = new bootstrap.Modal(partsModalElement);

                            const addPartBtn = modalBody.querySelector('#addPartBtn');
                            if (addPartBtn) {
                                addPartBtn.addEventListener('click', () => {
                                    partsModal.show();
                                });
                            }

                            initPartTaskHandlers(modalBody, partsModalElement, partsModal);
                        }
                    }

                    // Добавляем обработчик отправки формы
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);

                        fetch(this.action, {
                            method : 'POST',
                            body   : formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN'    : document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw response;
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    modal.querySelector('.btn-close').click();
                                    window.location.reload();
                                }
                            })
                            .catch(error => {
                                const errors = modal.querySelector('#formErrors');
                                errors.classList.remove('d-none');
                                error.json().then(data => {
                                    if (data.message) {
                                        errors.innerHTML = data.message;
                                    } else if (data.errors) {
                                        errors.innerHTML = Object.values(data.errors)
                                                                 .flat()
                                                                 .map(error => `<div>${ error }</div>`)
                                                                 .join('');
                                    } else {
                                        errors.innerHTML = error.status + ' ' + error.statusText;
                                    }
                                }).catch(e => {
                                    errors.innerHTML = 'Unknown error';
                                });
                            });
                    });
                });
        });

        modal.addEventListener('hidden.bs.modal', function() {
            if (partsModal) {
                partsModal.dispose();
                const partsModalElement = document.querySelector('#partsModal');
                if (partsModalElement) {
                    partsModalElement.remove();
                }
            }
        });
    };

    // Выносим обработчики в отдельную функцию
    const initPartTaskHandlers = (modalBody, partsModalElement, partsModal) => {
        const selectedParts = modalBody.querySelector('#selectedParts');
        let partIndex = selectedParts.children.length;

        // Обработчики выбора части теперь ищутся в перемещенном окне
        partsModalElement.querySelectorAll('.select-part').forEach(button => {
            button.addEventListener('click', function() {
                const partId = this.dataset.partId;
                if (!selectedParts.querySelector(`[data-part-id="${ partId }"]`)) {
                    addPartToForm({
                        partId     : this.dataset.partId,
                        partName   : this.dataset.partName,
                        partVersion: this.dataset.partVersion
                    });
                }
                partsModal.hide();
            });
        });

        // Обработчик удаления части
        selectedParts.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-part');
            if (removeBtn) {
                removeBtn.closest('.list-group-item').remove();
            }
        });

        function addPartToForm(partData) {
            const html = `
            <div class="list-group-item" data-part-id="${ partData.partId }">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#${ partData.partId }</strong>
                        ${ partData.partName }
                        <span class="text-muted">(v${ partData.partVersion })
                        ${ partData.partVersionDate ? ` от ${ partData.partVersionDate }` : '' })</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="parts[${ partIndex }][id]" value="${ partData.partId }">
                        <input type="number" class="form-control form-control-sm w-auto"
                               name="parts[${ partIndex }][count_per_set]"
                               placeholder="Кількість на набір"
                               required
                               min="1"
                               style="width: 100px !important;"
                               value="1">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-part">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            selectedParts.insertAdjacentHTML('beforeend', html);
            partIndex++;
        }
    };

    // Initialize modals if they exist
    document.querySelectorAll('.modal[data-type="formModal"]').forEach(modal => {
        initModalForm(modal);
    });

});


function initToggleRows(options = {}) {
    const {
        toggleSelector,      // Селектор кнопки раскрытия
        rowSelector,         // Селектор строки с содержимым
        cookiePrefix,        // Префикс для имени куки
        idAttribute = 'id',  // Атрибут для получения ID
        duration = 1 / 24    // Срок хранения куки в днях
    } = options;

    // Добавим слушатель для кнопок внутри строк
    document.querySelectorAll(`${ toggleSelector } button, ${ toggleSelector } a`).forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // Останавливаем всплытие события
        });
    });


    document.querySelectorAll(toggleSelector).forEach(button => {
        const id = button.dataset[idAttribute];
        const row = document.querySelector(`${ rowSelector }[data-parent-id="${ id }"]`);
        const cookieName = `${ cookiePrefix }_expanded_${ id }`;
        const isExpanded = Cookies.get(cookieName);

        if (!row) {
            return;
        }

        if (isExpanded) {
            row.classList.remove('d-none');
            button.querySelector('i').classList.remove('bi-chevron-right');
            button.querySelector('i').classList.add('bi-chevron-down');
        }

        button.addEventListener('click', function() {
            const icon = this.querySelector('i');

            row.classList.toggle('d-none');
            icon.classList.toggle('bi-chevron-right');
            icon.classList.toggle('bi-chevron-down');

            if (!row.classList.contains('d-none')) {
                Cookies.set(cookieName, '1', {expires: duration});
            } else {
                Cookies.remove(cookieName);
            }
        });
    });
}

window.initToggleRows = initToggleRows;

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.update-printed-btn');
    if (!btn) return;
    const partTaskId = btn.dataset.partTaskId;
    Swal.fire({
        title: 'Введіть кількість додаваних копій',
        input: 'number',
        // inputAttributes: { min: 1 },
        showCancelButton: true,
        confirmButtonText: 'Добавить'
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/print/task-parts/add-printed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    printed_count: parseInt(result.value),
                    part_task_id: partTaskId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Можно обновить UI или перезагрузить страницу
                    location.reload();
                }
            });
        }
    });
});

