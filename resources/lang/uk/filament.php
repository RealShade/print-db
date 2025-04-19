<?php

return [
    // Загальні фрази
    'title' => 'Філаменти',
    'form_title' => 'Форма філаменту',
    'add' => 'Додати філамент',
    'not_found_or_not_owned' => 'Філамент не знайдено або він не належить вам',

    // Основні поля
    'name' => 'Назва',
    'color' => 'Колір',
    'colors' => 'Кольори',
    'colors_placeholder' => 'Червоний, білий, чорний...',
    'colors_help' => 'Введіть кольори, розділені комами',
    'density' => 'Щільність',
    'cost' => 'Вартість',

    // Дії
    'action' => [
        'delete' => [
            'confirm' => 'Ви впевнені, що хочете видалити цей філамент?',
        ],
    ],

    // Тип філаменту
    'type' => [
        'field' => 'Тип',
        'title' => 'Типи філаментів',
        'form_title' => 'Форма типу філаменту',
        'add' => 'Додати тип філаменту',
        'not_found_or_not_owned' => 'Тип філаменту не знайдено або він не належить вам',
        'name' => 'Назва',
        'action' => [
            'delete' => [
                'confirm' => 'Ви впевнені, що хочете видалити цей тип філаменту?',
            ],
        ],
    ],

    // Виробник філаменту
    'vendor' => [
        'field' => 'Виробник',
        'title' => 'Виробники філаментів',
        'form_title' => 'Форма виробника філаменту',
        'add' => 'Додати виробника філаменту',
        'not_found_or_not_owned' => 'Виробника філаменту не знайдено або він не належить вам',
        'name' => 'Назва',
        'rate' => 'Рейтинг',
        'comment' => 'Коментар',
        'filaments_count' => 'Кількість філаментів',
        'action' => [
            'delete' => [
                'confirm' => 'Ви впевнені, що хочете видалити цього виробника філаменту?',
            ],
        ],
    ],

    // Упаковка філаменту
    'packaging' => [
        'title' => 'Типи упаковки філаменту',
        'form_title' => 'Форма типу упаковки філаменту',
        'add' => 'Додати тип упаковки філаменту',
        'not_found_or_not_owned' => 'Тип упаковки філаменту не знайдено або він не належить вам',
        'name' => 'Назва',
        'weight' => 'Вага',
        'description' => 'Опис',
        'action' => [
            'delete' => [
                'confirm' => 'Ви впевнені, що хочете видалити цей тип упаковки філаменту?',
            ],
        ],
    ],

    // Котушка філаменту
    'spool' => [
        'title' => 'Котушка філаменту',
        'form_title' => 'Форма котушки філаменту',
        'add' => 'Додати котушку філаменту',
        'not_found_or_not_owned' => 'Котушку філаменту не знайдено або вона не належить вам',
        'name' => 'Назва',
        'filament' => 'Філамент',
        'packaging' => 'Упаковка',
        'weight_initial' => 'Початкова вага',
        'weight_used' => 'Використана вага',
        'remaining_weight' => 'Залишок ваги',
        'usage_percent' => 'Відсоток використання',
        'date_first_used' => 'Перше використання',
        'date_last_used' => 'Останнє використання',
        'cost' => 'Вартість',
        'quantity' => 'Кількість котушок',
        'quantity_hint' => 'Буде додано стільки котушок з однаковими параметрами',
        'select_prompt' => '-- виберіть котушку філаменту --',
        'action' => [
            'delete' => [
                'confirm' => 'Ви впевнені, що хочете видалити цю котушку філаменту?',
            ],
            'archive' => [
                'title' => 'Архів котушок',
                'confirm' => 'Перемістити котушку в архів?',
                'confirm_archived' => 'Повернути котушку з архіву?',
            ],
        ],
    ],
];
