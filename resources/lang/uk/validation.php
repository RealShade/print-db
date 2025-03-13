<?php

return [
    'accepted'             => 'Ви повинні прийняти :attribute.',
    'active_url'           => 'Поле ":attribute" не є правильним URL.',
    'after'                => 'Поле ":attribute" має бути датою після :date.',
    'after_or_equal'       => 'Поле ":attribute" має бути датою після або рівною :date.',
    'alpha'                => 'Поле ":attribute" може містити лише літери.',
    'alpha_dash'           => 'Поле ":attribute" може містити лише літери, цифри, дефіс і підкреслення.',
    'alpha_num'            => 'Поле ":attribute" може містити лише літери і цифри.',
    'array'                => 'Поле ":attribute" має бути масивом.',
    'before'               => 'Поле ":attribute" має бути датою перед :date.',
    'before_or_equal'      => 'Поле ":attribute" має бути датою перед або рівною :date.',
    'between'              => [
        'numeric' => 'Поле ":attribute" має бути між :min і :max.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути між :min і :max кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути між :min і :max.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути між :min і :max.',
    ],
    'boolean'              => 'Поле ":attribute" повинне містити логічний тип.',
    'confirmed'            => 'Поле ":attribute" не збігається з підтвердженням.',
    'date'                 => 'Поле ":attribute" не є датою.',
    'date_equals'          => 'Поле ":attribute" має бути датою рівною :date.',
    'date_format'          => 'Поле ":attribute" не відповідає формату :format.',
    'different'            => 'Поля ":attribute" і :other повинні бути різними.',
    'digits'               => 'Довжина цифрового поля ":attribute" повинна бути :digits.',
    'digits_between'       => 'Довжина цифрового поля ":attribute" повинна бути між :min і :max.',
    'dimensions'           => 'Поле ":attribute" містить неприпустимі розміри зображення.',
    'distinct'             => 'Поле ":attribute" містить значення, яке дублюється.',
    'email'                => 'Поле ":attribute" повинне містити коректну електронну адресу.',
    'exists'               => 'Вибране значення для ":attribute" не коректне.',
    'file'                 => 'Поле ":attribute" повинне містити файл.',
    'filled'               => 'Поле ":attribute" є обов\'язковим для заповнення.',
    'gt'                   => [
        'numeric' => 'Поле ":attribute" має бути більше ніж :value.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути більше ніж :value кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути більше ніж :value.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути більше ніж :value.',
    ],
    'gte'                  => [
        'numeric' => 'Поле ":attribute" має бути більше або дорівнювати :value.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути більше або дорівнювати :value кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути більше або дорівнювати :value.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути більше або дорівнювати :value.',
    ],
    'image'                => 'Поле ":attribute" повинне містити зображення.',
    'in'                   => 'Вибране значення для ":attribute" помилкове.',
    'in_array'             => 'Значення поля ":attribute" не існує в :other.',
    'integer'              => 'Поле ":attribute" повинне містити ціле число.',
    'ip'                   => 'Поле ":attribute" повинне містити IP адресу.',
    'ipv4'                 => 'Поле ":attribute" повинне містити IPv4 адресу.',
    'ipv6'                 => 'Поле ":attribute" повинне містити IPv6 адресу.',
    'json'                 => 'Поле ":attribute" повинне містити JSON рядок.',
    'lt'                   => [
        'numeric' => 'Поле ":attribute" має бути менше ніж :value.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути менше ніж :value кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути менше ніж :value.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути менше ніж :value.',
    ],
    'lte'                  => [
        'numeric' => 'Поле ":attribute" має бути менше або дорівнювати :value.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути менше або дорівнювати :value кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути менше або дорівнювати :value.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути менше або дорівнювати :value.',
    ],
    'max'                  => [
        'numeric' => 'Поле ":attribute" має бути не більше ніж :max.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути не більше ніж :max кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути не більше ніж :max.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути не більше ніж :max.',
    ],
    'mimes'                => 'Поле ":attribute" повинне містити файл одного з типів: :values.',
    'mimetypes'            => 'Поле ":attribute" повинне містити файл одного з типів: :values.',
    'min'                  => [
        'numeric' => 'Поле ":attribute" має бути не менше ніж :min.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути не менше ніж :min кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути не менше ніж :min.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути не менше ніж :min.',
    ],
    'not_in'               => 'Вибране значення для ":attribute" помилкове.',
    'not_regex'            => 'Формат поля ":attribute" помилковий.',
    'numeric'              => 'Поле ":attribute" повинне містити число.',
    'present'              => 'Поле ":attribute" повинне бути присутнє.',
    'regex'                => 'Формат поля ":attribute" помилковий.',
    'required'             => 'Поле ":attribute" є обов\'язковим для заповнення.',
    'required_if'          => 'Поле ":attribute" є обов\'язковим для заповнення, коли :other є :value.',
    'required_unless'      => 'Поле ":attribute" є обов\'язковим для заповнення, коли :other не є :values.',
    'required_with'        => 'Поле ":attribute" є обов\'язковим для заповнення, коли присутнє :values.',
    'required_with_all'    => 'Поле ":attribute" є обов\'язковим для заповнення, коли присутні :values.',
    'required_without'     => 'Поле ":attribute" є обов\'язковим для заповнення, коли відсутнє :values.',
    'required_without_all' => 'Поле ":attribute" є обов\'язковим для заповнення, коли жодне з :values не присутнє.',
    'same'                 => 'Поля ":attribute" і :other мають збігатися.',
    'size'                 => [
        'numeric' => 'Поле ":attribute" має бути довжиною :size.',
        'file'    => 'Розмір файлу в полі ":attribute" має бути :size кілобайт.',
        'string'  => 'Кількість символів в полі ":attribute" має бути :size.',
        'array'   => 'Кількість елементів в полі ":attribute" має бути :size.',
    ],
    'starts_with'          => 'Поле ":attribute" має починатися з одного з наступних значень: :values.',
    'string'               => 'Поле ":attribute" повинне бути рядком.',
    'timezone'             => 'Поле ":attribute" повинне містити коректну часову зону.',
    'unique'               => 'Поле ":attribute" вже зайнято.',
    'uploaded'             => 'Завантаження поля ":attribute" не вдалося.',
    'url'                  => 'Формат поля ":attribute" помилковий.',
    'uuid'                 => 'Поле ":attribute" має бути коректним UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'email'           => [
            'unique' => 'Ця електронна адреса вже зареєстрована.',
        ],
        'type_employment' => [
            'required' => 'Оберіть принаймні одну позицію.',
        ],
        'captcha'         => 'Капча обов\'язкова.',
        'file'            => [
            'extension' => 'Файл повинен бути одного з наступних типів: :values.',
            'exists'    => 'Такий файл вже був завантажений.',
            'not_found' => 'Файл не знайдено.',
            'denied'    => 'Ви не маєте доступу до цього файлу.',
        ],
        'kodifikator'     => 'Населений пункт вказано невірно.',
        'ajax'            => [
            'answer' => [
                'unknown'          => [
                    'title' => 'Помилка',
                    'text'  => 'Невідома відповідь сервера.',
                ],
                'not_authorized'    => [
                    'title' => 'Помилка авторизації',
                    'text'  => 'Ви не авторизовані. Будь ласка, увійдіть в систему.',
                ],
                'forbidden'        => [
                    'title' => 'Заборонено',
                    'text'  => 'Ви не маєте прав для виконання цієї дії.',
                ],
                'not_found'        => [
                    'title' => 'Не знайдено',
                    'text'  => 'Ресурс, який ви шукаєте, не знайдено.',
                ],
                'validation_error' => [
                    'title' => 'Помилка валідації',
                    'text'  => 'Будь ласка, виправте помилки в формі.',
                ],
                'error'            => [
                    'title' => 'Помилка',
                    'text'  => 'Виникла помилка [:status]. Будь ласка, спробуйте ще раз. (:error)',
                ],
                'file_too_large' => [
                    'title' => 'Помилка',
                    'text'  => 'Розмір файлу перевищує допустимий ліміт. Максимальний розмір файлу: ' . ini_get('upload_max_filesize'),
                ],
                'url_too_long'   => [
                    'title' => 'Помилка',
                    'text'  => 'URL занадто довгий.',
                ],
                'unsupported_media_type' => [
                    'title' => 'Помилка',
                    'text'  => 'Неприпустимий тип файлу.',
                ],
            ],
        ],
        'enum'            => [
            'value'   => 'Поле ":attribute" має невірне значення.',
            'unknown' => 'Невідомий клас переліку :enum_class.',
        ],
        'salary_from'     => [
            'integer' => 'Мінімальна заробітна плата повинне містити ціле число.',
        ],
        'salary_to'       => [
            'integer' => 'Максимальна заробітна плата повинне містити ціле число.',
        ],
        'category'        => [
            'max' => 'Кількість категорій не може перевищувати :max.',
        ],
        'text' => [
            'required' => 'Текст має бути вказаний'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'work_experience' => 'досвід роботи',
    ],
];
