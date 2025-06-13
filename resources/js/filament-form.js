export function initFilamentForm() {
    const colorBlocks = document.getElementById('color-blocks');
    if (!colorBlocks) {
        return;
    }

    const addColorButton = document.getElementById('add-color-block');
    if (addColorButton) {
        addColorButton.addEventListener('click', function() {
            if (colorBlocks.children.length < 8) {
                const div = document.createElement('div');
                div.className = 'color-block';
                div.innerHTML = `
                    <div class="color-picker"></div>
                    <input type="hidden" name="colors[]" class="color-value">
                    <button type="button" class="btn btn-sm btn-danger remove-color-block">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                colorBlocks.appendChild(div);
                // Не инициализируем Spectrum здесь!
                // window.initFilamentColorPickers() должен вызываться после вставки формы
                initRemoveColorButtons();
            }
        });
    }

    initRemoveColorButtons();
}

function initRemoveColorButtons() {
    document.querySelectorAll('.remove-color-block').forEach(button => {
        button.removeEventListener('click', removeColorBlock);
        button.addEventListener('click', removeColorBlock);
    });
}

function removeColorBlock() {
    const colorBlocks = document.getElementById('color-blocks');
    if (colorBlocks.children.length > 0) {
        this.closest('.color-block').remove();
    }
}
