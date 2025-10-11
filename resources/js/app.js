import Swal from 'sweetalert2';
import Cookies from 'js-cookie';
import trans from './translations';
import 'spectrum-colorpicker/spectrum.css';
import $ from 'jquery';
import 'spectrum-colorpicker';
import {initFilamentForm} from "./filament-form";

window.Swal = Swal;
window.Cookies = Cookies;
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
                icon             : 'question',
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

    // AJAX-queries for buttons
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
                            text : data.message || trans.get('common.error.something_went_wrong')
                        });
                    });
                });
        };

        if (button.dataset.confirm === 'true') {
            Swal.fire({
                title            : button.dataset.confirmTitle,
                text             : button.dataset.confirmText,
                icon             : 'question',
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

                    if (form.id !== 'partForm') {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            const formData = new FormData(this);

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                                                .map(error => `<div>${error}</div>`)
                                                .join('');
                                        } else {
                                            errors.innerHTML = error.status + ' ' + error.statusText;
                                        }
                                    }).catch(e => {
                                        errors.innerHTML = 'Unknown error';
                                    });
                                });
                        });
                    }

                    // Повторная инициализация всех динамических элементов
                    // modalBody.querySelectorAll('script').forEach(oldScript => {
                    //     let newScript = document.createElement('script');
                    //     if (oldScript.src) {
                    //         newScript.src = oldScript.src;
                    //         newScript.onload = () => console.log(`Загружен: ${oldScript.src}`);
                    //     } else {
                    //         newScript.text = oldScript.innerHTML;
                    //     }
                    //     document.body.appendChild(newScript);
                    // });

                    // Инициализируем обработчики форм, копирования и т. д.
                    document.dispatchEvent(new Event('modalContentLoaded'));
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

    // Initialize modals if they exist
    document.querySelectorAll('.modal[data-type="formModal"]').forEach(modal => {
        initModalForm(modal);
    });

});


function initToggleRows(options = {}) {
    const {
        toggleSelector,
        rowSelector,
        cookiePrefix,
        idAttribute = 'id',
        duration = 1    // days
    } = options;

    // Удаляем этот блок, так как он нам больше не нужен
    // document.querySelectorAll(`${toggleSelector} button:not([data-transport]), ${toggleSelector} a:not([data-transport])`).forEach(button => {
    //     button.addEventListener('click', (e) => {
    //         e.stopPropagation();
    //     });
    // });

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
            button.querySelector('i.toggle-icon').classList.remove('bi-chevron-right');
            button.querySelector('i.toggle-icon').classList.add('bi-chevron-down');
        }

        button.addEventListener('click', function(e) {
            // Проверяем, что клик был по самой строке, шеврону или ячейке с шевроном,
            // но не по кнопкам или другим интерактивным элементам
            if (e.target.closest('button, a, .btn, [data-transport]')) {
                return;
            }

            const icon = this.querySelector('i.toggle-icon');
            if (!icon) {
                return;
            }

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

function initCatalogTree() {
    document.querySelectorAll('.toggle-catalog').forEach(button => {
        const id = button.dataset.id;
        const childrenContainer = document.querySelector(`.catalog-children[data-parent-id="${ id }"]`);
        const cookieName = `catalog_expanded_${ id }`;
        const isExpanded = Cookies.get(cookieName);

        if (!childrenContainer) {
            return;
        }

        if (isExpanded === '1') {
            childrenContainer.classList.remove('d-none');
            button.querySelector('.toggle-icon').classList.remove('bi-chevron-right');
            button.querySelector('.toggle-icon').classList.add('bi-chevron-down');
        }

        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const icon = this.querySelector('.toggle-icon');
            if (!icon) {
                return;
            }

            childrenContainer.classList.toggle('d-none');
            icon.classList.toggle('bi-chevron-right');
            icon.classList.toggle('bi-chevron-down');

            if (!childrenContainer.classList.contains('d-none')) {
                Cookies.set(cookieName, '1', {expires: 30});
            } else {
                Cookies.remove(cookieName);
            }
        });
    });
}

function initSelectPartDropdown() {
    const dropdownInputs = document.querySelectorAll('.custom-select-dropdown .form-control');
    dropdownInputs.forEach(dropdownInput => {
        const hiddenInput = dropdownInput.closest('.custom-select-dropdown').querySelector('input[type="hidden"]');
        const clearButton = dropdownInput.closest('.custom-select-dropdown').querySelector('.btn-clear-input');
        const dropdownItems = dropdownInput.closest('.custom-select-dropdown').querySelectorAll('.dropdown-item');

        dropdownItems.forEach(item => {
            item.addEventListener('click', function() {
                const selectedText = this.querySelector('strong').textContent;
                const selectedId = this.dataset.id;

                // Устанавливаем выбранное значение
                dropdownInput.value = selectedText;
                hiddenInput.value = selectedId;

                // Закрываем выпадающий список
                const dropdownMenu = this.closest('.dropdown-menu');
                dropdownMenu.classList.remove('show');
            });
        });

        // Очищаем значения при нажатии на кнопку очистки
        clearButton.addEventListener('click', function() {
            dropdownInput.value = '';
            hiddenInput.value = '';
        });

        // Предотвращаем фокусировку текстового поля
        dropdownInput.addEventListener('focus', function(event) {
            event.target.blur();
        });
    });
}

function initHoverControls() {
    // Обработчики событий для элементов с атрибутом data-hover
    document.querySelectorAll('[data-hover]').forEach(parent => {
        const hoverIdentifier = parent.dataset.hover;

        parent.addEventListener('mouseenter', () => {
            // Ищем только внутри текущего родительского элемента
            parent.querySelectorAll(`[data-hover-target="${hoverIdentifier}"]`).forEach(control => {
                control.classList.add('hover-show');
            });
        });

        parent.addEventListener('mouseleave', () => {
            // Ищем только внутри текущего родительского элемента
            parent.querySelectorAll(`[data-hover-target="${hoverIdentifier}"]`).forEach(control => {
                control.classList.remove('hover-show');
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initCatalogTree();
    initHoverControls();
});

// Функция инициализации загрузки файлов через Dropzone
function initializeDropzoneUploader() {
    // Проверяем существование формы для деталей на странице
    const partForm = document.getElementById('partForm');
    if (!partForm) return;

    console.log('Инициализация загрузчика файлов через Dropzone');

    // Проверяем, загружена ли библиотека Dropzone
    if (typeof Dropzone !== 'undefined') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Определяем URL для загрузки чанков
        // Сначала ищем его в data-атрибуте кнопки, которая открыла модальное окно
        let uploadUrl = '';

        // Проверяем, открыто ли модальное окно
        const modal = partForm.closest('.modal');
        if (modal) {
            // Получаем ID кнопки, которая открыла модальное окно
            const modalButton = document.querySelector(`[data-bs-target="#${modal.id}"][data-upload-url]`);
            if (modalButton && modalButton.dataset.uploadUrl) {
                uploadUrl = modalButton.dataset.uploadUrl;
                console.log('Получен URL загрузки из data-upload-url:', uploadUrl);
            }
        }

        // Если URL не найден, используем стандартный путь
        if (!uploadUrl) {
            uploadUrl = '/print/parts/upload-chunk';
            console.log('Используется стандартный URL загрузки:', uploadUrl);
        }

        console.log('URL для загрузки файлов:', uploadUrl);

        // Находим или создаем контейнер для Dropzone
        let dropzoneContainer = document.getElementById('stl-dropzone');

        // Если контейнер не существует, создаем его на основе существующего поля input
        if (!dropzoneContainer) {
            const fileInput = document.getElementById('stl_file');
            if (!fileInput) {
                console.error('Элемент #stl_file не найден!');
                return;
            }

            // Создаем контейнер для Dropzone
            dropzoneContainer = document.createElement('div');
            dropzoneContainer.id = 'stl-dropzone';
            dropzoneContainer.className = 'dropzone mb-3 custom-dropzone';

            // Заменяем input на контейнер Dropzone
            fileInput.parentNode.insertBefore(dropzoneContainer, fileInput);
            fileInput.style.display = 'none'; // Скрываем исходный input
        }

        // Находим скрытые поля для хранения информации о загруженном файле
        const chunkFilePathInput = document.getElementById('chunk_file_path');
        const chunkOriginalNameInput = document.getElementById('chunk_original_name');

        if (!chunkFilePathInput || !chunkOriginalNameInput) {
            console.error('Не найдены скрытые поля для хранения информации о файле');
            return;
        }

        // Настройка Dropzone
        Dropzone.autoDiscover = false;

        // Создаем экземпляр Dropzone
        const myDropzone = new Dropzone('#stl-dropzone', {
            url: uploadUrl,
            chunking: true,
            forceChunking: true,
            chunkSize: 1024 * 1024, // 1 MB
            parallelChunkUploads: false, // Отключаем параллельную загрузку чанков
            maxFiles: 1,
            maxFilesize: 250, // MB
            acceptedFiles: '.stl',
            addRemoveLinks: true,
            retryChunks: true, // Повторять загрузку чанков при ошибке
            retryChunksLimit: 3, // Максимальное количество повторных попыток
            timeout: 120000, // Увеличиваем таймаут до 2 минут
            dictDefaultMessage: "Перетащите STL-файл сюда или нажмите для выбора",
            dictRemoveFile: "Удалить файл",
            dictCancelUpload: "Отменить загрузку",
            dictFileTooBig: "Файл слишком большой ({{filesize}}MB). Максимальный размер: {{maxFilesize}}MB.",
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            // Параметры для совместимости с бэкендом
            paramName: 'stl_file',
        });

        // Обработчики событий Dropzone
        myDropzone.on('addedfile', function(file) {
            console.log('Файл добавлен:', file.name);

            const submitButton = partForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }

            // Очищаем предыдущие данные о загруженном файле
            chunkFilePathInput.value = '';
            chunkOriginalNameInput.value = '';
        });

        myDropzone.on('uploadprogress', function(file, progress) {
            console.log('Прогресс загрузки:', progress.toFixed(0) + '%');
        });

        myDropzone.on('success', function(file, response) {
            console.log('Файл успешно загружен:', response);

            const submitButton = partForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
            }
            // Сохраняем информацию о загруженном файле
            if (response.success) {
                // Если это последний чанк или обычная загрузка файла
                if (response.path && response.original_name) {
                    chunkFilePathInput.value = response.path;
                    chunkOriginalNameInput.value = response.original_name;

                    console.log('Установлены значения:');
                    console.log('chunk_file_path:', chunkFilePathInput.value);
                    console.log('chunk_original_name:', chunkOriginalNameInput.value);
                } else {
                    console.log('Чанк успешно загружен, ожидание завершения загрузки');
                }
            }
        });

        myDropzone.on('error', function(file, errorMessage, xhr) {

                    // Разблокируем кнопку сохранения и возвращаем оригинальный текст
                    const submitButton = partForm.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
            console.error('Ошибка загрузки файла:', errorMessage);

            // Удаляем файл из очереди
            myDropzone.removeFile(file);

            // Показываем сообщение об ошибке
            const errors = partForm.querySelector('#formErrors');
            if (errors) {
                errors.classList.remove('d-none');
                errors.innerHTML = typeof errorMessage === 'string'
                    ? errorMessage
                    : 'Произошла ошибка при загрузке файла';
            } else {
                alert('Ошибка загрузки файла: ' + file.name);
            }
        });

        // Модификация обработчика отправки формы
        // Обработка отмены загрузки
        myDropzone.on('canceled', function(file) {
            console.log('Загрузка отменена:', file.name);

            // Разблокируем кнопку сохранения и возвращаем оригинальный текст
            const submitButton = partForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
            }
        });

        // Обработка удаления файла
        myDropzone.on('removedfile', function(file) {
            console.log('Файл удален из очереди:', file.name);

            // Очищаем данные о загруженном файле
            chunkFilePathInput.value = '';
            chunkOriginalNameInput.value = '';

            // Разблокируем кнопку сохранения и возвращаем оригинальный текст
            const submitButton = partForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
            }
        });

        if (partForm) {
            console.log('Добавляем обработчик для формы:', partForm);

            // Отменяем стандартный обработчик и заменяем своим
            partForm.onsubmit = function(e) {
                e.preventDefault();

                console.log('Отправка формы с данными о загруженном файле');
                console.log('chunk_file_path:', chunkFilePathInput.value);
                console.log('chunk_original_name:', chunkOriginalNameInput.value);

                // Создаем новый объект FormData из формы
                const formData = new FormData(this);

                // Добавляем данные о загруженном файле в FormData, если они есть
                if (chunkFilePathInput.value && chunkOriginalNameInput.value) {
                    console.log('Добавляем данные о загруженном файле в FormData');
                    formData.set('chunk_file_path', chunkFilePathInput.value);
                    formData.set('chunk_original_name', chunkOriginalNameInput.value);
                }

                // Получаем URL для отправки формы
                const url = this.action;
                console.log('URL для отправки формы:', url);

                // Получаем метод отправки
                let method = this.method.toUpperCase();
                // Если есть скрытое поле _method, используем его значение
                const methodField = this.querySelector('input[name="_method"]');
                if (methodField) {
                    method = methodField.value.toUpperCase();
                }
                console.log('Метод отправки формы:', method);

                // Отправляем форму через fetch
                fetch(url, {
                    method: 'POST', // Всегда используем POST, метод указываем в заголовке X-HTTP-Method-Override
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': method
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
                        // Закрываем модальное окно, если это необходимо
                        const modal = partForm.closest('.modal');
                        if (modal) {
                            const closeButton = modal.querySelector('.btn-close');
                            if (closeButton) {
                                closeButton.click();
                            }
                        }

                        // Перезагружаем страницу для отображения изменений
                        window.location.reload();
                    }
                })
                .catch(error => {
                    const errors = partForm.querySelector('#formErrors');
                    if (errors) {
                        errors.classList.remove('d-none');
                        error.json().then(data => {
                            if (data.message) {
                                errors.innerHTML = data.message;
                            } else if (data.errors) {
                                errors.innerHTML = Object.values(data.errors)
                                                        .flat()
                                                        .map(error => `<div>${error}</div>`)
                                                        .join('');
                            } else {
                                errors.innerHTML = error.status + ' ' + error.statusText;
                            }
                        }).catch(e => {
                            errors.innerHTML = 'Unknown error';
                        });
                    } else {
                        console.error('Ошибка при отправке формы:', error);
                    }
                });
            };
        }
    } else {
        console.error('Библиотека Dropzone не загружена');
    }
}

// Обработчик события modalContentLoaded
document.addEventListener('modalContentLoaded', function() {
    initFilamentForm();
    initCatalogTree();
    initHoverControls();

    // Инициализируем dropdown для select-part
    initSelectPartDropdown();

    // Инициализируем загрузку файлов через Dropzone
    if (typeof Dropzone !== 'undefined') {
        initializeDropzoneUploader();
    } else {
        // Если библиотека еще не загружена, загрузим ее и инициализируем
        const script = document.createElement('script');
        script.src = '/assets/js/libs/dropzone.min.js';
        script.onload = function() {
            initializeDropzoneUploader();
        };
        document.head.appendChild(script);
    }
});

// Глобальная функция для инициализации Spectrum Colorpicker с палитрой
window.initFilamentColorPickers = function() {
    document.querySelectorAll('.color-picker:not(.pickr-initialized)').forEach(element => {
        element.classList.add('pickr-initialized');
        const colorBlock = element.closest('.color-block');
        const defaultColor = element.dataset.defaultColor || 'rgba(127, 127, 127, 0.5)';
        const inputElement = colorBlock.querySelector('.color-value');
        const previewElement = colorBlock.querySelector('.filament-color-preview') || element;
        let manualInput = colorBlock.querySelector('.manual-color-input');
        if (!manualInput) {
            manualInput = document.createElement('input');
            manualInput.type = 'text';
            manualInput.className = 'form-control form-control-sm manual-color-input mt-1';
            manualInput.placeholder = 'rgba или hex';
            manualInput.value = defaultColor;
            element.parentNode.insertBefore(manualInput, element.nextSibling);
        }
        previewElement.style.backgroundColor = defaultColor;
        $(element).spectrum({
            color: defaultColor,
            showAlpha: true,
            showInput: true,
            allowEmpty: true,
            preferredFormat: "rgba",
            showPalette: true,
            palette: window.filamentColorsPalette || [],
            change: function(color) {
                const rgba = color ? color.toRgbString() : '';
                inputElement.value = rgba;
                previewElement.style.backgroundColor = rgba;
                manualInput.value = rgba;
            },
            move: function(color) {
                const rgba = color ? color.toRgbString() : '';
                inputElement.value = rgba;
                previewElement.style.backgroundColor = rgba;
                manualInput.value = rgba;
            }
        });
        if (defaultColor) {
            inputElement.value = defaultColor;
        }
        // Обработка ручного ввода
        manualInput.addEventListener('change', function() {
            const val = manualInput.value.trim();
            inputElement.value = val;
            previewElement.style.backgroundColor = val;
            $(element).spectrum('set', val);
        });
    });
};

document.addEventListener('modalContentLoaded', function() {
    if (window.initFilamentColorPickers) {
        window.initFilamentColorPickers();
    }
});

// Инициализация при прямой загрузке формы
// document.addEventListener('DOMContentLoaded', function() {
//     initFilamentForm();
// });

window.initToggleRows = initToggleRows;
window.initFilamentForm = initFilamentForm;
window.initCatalogTree = initCatalogTree;
window.initHoverControls = initHoverControls;

