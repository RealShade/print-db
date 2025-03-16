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

    // Task parts toggle
    document.querySelectorAll('.toggle-parts').forEach(button => {
        const taskId = button.dataset.taskId;
        const cookieName = `task_expanded_${ taskId }`;
        const isExpanded = Cookies.get(cookieName);

        if (isExpanded) {
            const partsRow = document.querySelector(`tr.parts-row[data-parent-id="${ taskId }"]`);
            const icon = button.querySelector('i');

            partsRow.classList.remove('d-none');
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        }

        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const partsRow = document.querySelector(`tr.parts-row[data-parent-id="${ taskId }"]`);
            const icon = this.querySelector('i');
            const cookieName = `task_expanded_${ taskId }`;

            partsRow.classList.toggle('d-none');
            icon.classList.toggle('bi-chevron-right');
            icon.classList.toggle('bi-chevron-down');

            if (!partsRow.classList.contains('d-none')) {
                Cookies.set(cookieName, '1', {expires: 1 / 24}); // 1 час
            } else {
                Cookies.remove(cookieName);
            }
        });
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
            console.log(method);

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
                                error.json().then(data => {
                                    const errors = modal.querySelector('#formErrors');
                                    errors.classList.remove('d-none');
                                    errors.innerHTML = Object.values(data.errors || {})
                                                             .flat()
                                                             .map(error => `<div>${ error }</div>`)
                                                             .join('') || 'Виникла помилка при збереженні';
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
    const partModal = document.getElementById('partModal');
    const taskModal = document.getElementById('taskModal');
    const partTaskModal = document.getElementById('partTaskModal');

    if (partModal) {
        initModalForm(partModal);
    }
    if (taskModal) {
        initModalForm(taskModal);
    }
    if (partTaskModal) {
        initModalForm(partTaskModal);
    }

});
