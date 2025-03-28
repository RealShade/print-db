class Translator {
    constructor(locale) {
        this.locale = locale || document.documentElement.lang || 'uk';
        this.translations = window.translations || {};
    }

    get(key, replacements = {}) {
        // Разбираем ключ вида "app.add_copies.title"
        const parts = key.split('.');
        const fileKey = parts[0];
        const translationKey = parts.slice(1).join('.');

        let translation;

        // Доступ к нужному файлу переводов и ключу
        if (this.translations[this.locale] &&
            this.translations[this.locale][fileKey]) {
            // Для вложенных ключей
            translation = parts.slice(1).reduce((obj, i) =>
                obj && obj[i] !== undefined ? obj[i] : null,
                this.translations[this.locale][fileKey]
            );
        }

        if (!translation) {
            return key; // Возвращаем ключ, если перевод не найден
        }

        // Заменяем плейсхолдеры
        return this._applyReplacements(translation, replacements);
    }

    _applyReplacements(translation, replacements) {
        for (const key in replacements) {
            translation = translation.replace(
                new RegExp(`:${key}`, 'g'),
                replacements[key]
            );
        }
        return translation;
    }

    has(key) {
        const parts = key.split('.');
        return parts.slice(1).reduce((obj, i) =>
            obj && obj[i] !== undefined ? obj[i] : null,
            this.translations[this.locale] &&
            this.translations[this.locale][parts[0]]
        ) !== null;
    }
}

export default new Translator();
