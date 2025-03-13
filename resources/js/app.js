import Swal from 'sweetalert2';

window.Swal = Swal;

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('.toggle-password');
    const password = document.querySelector('#password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icon
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    document.querySelectorAll('.confirm-block').forEach(function(form) {
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

    document.querySelectorAll('.confirm-delete').forEach(function(form) {
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

    // Обработка мобильного меню
    const toggleButtons = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    toggleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // Останавливаем всплытие события
            sidebar.classList.toggle('show');
        });
    });

    // Закрытие меню при клике вне его
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 &&
            !sidebar.contains(e.target) &&
            !e.target.closest('.sidebar-toggle')) { // Используем closest вместо classList.contains
            sidebar.classList.remove('show');
        }
    });
});
