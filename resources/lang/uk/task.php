<?php

return [
    'title'                  => 'Завдання',
    'form_title'             => 'Форма завдання',
    'external_id'            => 'Зовн. ID',
    'name'                   => 'Назва завдання',
    'count_set_planned'      => 'Комплекти',
    'status'                 => 'Статус',
    'parts'                  => 'Деталі',
    'count_per_set'          => 'У наборі',
    'count_printed'          => 'Надруковано',
    'count_required'         => 'Необхідно всього',
    'count_waiting'          => 'Очікується',
    'printing_count'         => 'Друкується',
    'add'                    => 'Додати завдання',
    'select_part'            => 'Вибір деталі',
    'parts_count'            => 'Моделей',
    'created_at'             => 'Створено',
    'api_format'             => 'Плашка для назви файлу',
    'enum'                   => [
        'status' => [
            'new'         => 'Нове',
            'in_progress' => 'В процесі',
            'cancelled'   => 'Скасовано',
            'printed'     => 'Надруковано',
            'completed'   => 'Виконано',
        ],
    ],
    'not_found_or_not_owned' => 'Вказане завдання не знайдене або вам не належить',
    'action'                => [
        'delete' => [
            'confirm' => 'Ви впевнені, що хочете видалити це завдання?',
            'success' => 'Завдання успішно видалено',
        ],
        'delete_part' => [
            'confirm' => 'Ви впевнені, що хочете видалити цю деталь з завдання?',
            'success' => 'Деталь успішно видалена з завдання',
        ],
    ]
];
