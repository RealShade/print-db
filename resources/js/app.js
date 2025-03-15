import Swal from 'sweetalert2';

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

    // Modal forms handling
    const initModalForm = (modal) => {
        let partsModal = null;
        let currentTaskButton = null;

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button) {
                currentTaskButton = button;
            }

            const action = currentTaskButton.dataset.action;
            const method = currentTaskButton.dataset.method || 'POST';
            const id = currentTaskButton.dataset.id;
            const formUrl = id
                ? `${currentTaskButton.dataset.editRoute}/${id}`
                : currentTaskButton.dataset.createRoute;

            fetch(formUrl)
                .then(response => response.text())
                .then(html => {
                    const modalBody = modal.querySelector('.modal-body');
                    modalBody.innerHTML = html;

                    const form = modalBody.querySelector('form');
                    form.action = action;
                    if (method === 'PUT') {
                        form.insertAdjacentHTML('afterbegin', '@method(\'PUT\')');
                    }

                    // Инициализация механизма выбора деталей для задачи
                    if (modal.id === 'taskModal') {
                        const partsModalContent = modalBody.querySelector('#partsModal');
                        if (partsModalContent) {
                            // Уничтожаем старый инстанс, если он существует
                            if (partsModal) {
                                partsModal.dispose();
                                const oldPartsModal = document.querySelector('#partsModal');
                                if (oldPartsModal) {
                                    oldPartsModal.remove();
                                }
                            }

                            // Перемещаем модальное окно деталей в конец body
                            document.body.appendChild(partsModalContent);

                            // Создаем новый инстанс модального окна
                            partsModal = new bootstrap.Modal(partsModalContent);

                            const addPartBtn = modalBody.querySelector('#addPartBtn');
                            if (addPartBtn) {
                                addPartBtn.addEventListener('click', () => {
                                    partsModal.show();
                                });
                            }

                            // Инициализируем обработчики после перемещения окна
                            initTaskPartsHandlers(modalBody, document.querySelector('#partsModal'), partsModal);
                        }
                    }
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
    const initTaskPartsHandlers = (modalBody, partsModalElement, partsModal) => {
        const selectedParts = modalBody.querySelector('#selectedParts');
        let partIndex = selectedParts.children.length;

        // Обработчики выбора части теперь ищутся в перемещенном окне
        partsModalElement.querySelectorAll('.select-part').forEach(button => {
            button.addEventListener('click', function() {
                const partId = this.dataset.partId;
                if (!selectedParts.querySelector(`[data-part-id="${partId}"]`)) {
                    addPartToForm({
                        partId: this.dataset.partId,
                        partName: this.dataset.partName,
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
                        <strong>${ partData.partName }</strong>
                        <span class="text-muted">(v${ partData.partVersion })</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="parts[${ partIndex }][id]" value="${ partData.partId }">
                        <input type="number" class="form-control form-control-sm w-auto"
                               name="parts[${ partIndex }][quantity_per_set]"
                               placeholder="Кількість на набір"
                               required
                               min="1"
                               style="width: 100px !important;">
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

    if (partModal) {
        initModalForm(partModal);
    }
    if (taskModal) {
        initModalForm(taskModal);
    }

});
