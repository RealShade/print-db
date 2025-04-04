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
                initColorPicker(div.querySelector('.color-picker'));
                initRemoveColorButtons();
            }
        });
    }

    initColorPickers();
    initRemoveColorButtons();
}

function initColorPickers() {
    document.querySelectorAll('.color-picker:not(.pickr-initialized)').forEach(element => {
        initColorPicker(element);
    });
}

function initColorPicker(element) {
    element.classList.add('pickr-initialized');

    const defaultColor = element.dataset.defaultColor || 'rgba(127, 127, 127, 0.5)';
    const inputElement = element.closest('.color-block').querySelector('.color-value');

    const pickr = Pickr.create({
        el        : element,
        theme     : 'classic',
        default   : defaultColor,
        components: {
            preview    : true,
            opacity    : true,
            hue        : true,
            interaction: {
                hex  : true,
                rgba : true,
                hsla : false,
                hsva : false,
                cmyk : false,
                input: true,
                clear: false,
                save : true
            }
        }
    });

    pickr.on('save', (color) => {
        inputElement.value = color.toRGBA().toString(0);
        pickr.hide();
    });

    if (defaultColor) {
        inputElement.value = defaultColor;
    }
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
