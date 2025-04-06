@extends('layouts.app')

@section('title', 'Документація по API')

@section('content')
<div class="container mt-4 documentation">
    <h1>Документація по API</h1>
    
    <h2>Аутентифікація</h2>
    <p>Всі запити до API повинні містити заголовок авторизації з токеном.</p>
    <pre><code>Authorization: Bearer YOUR_API_TOKEN</code></pre>
    <p>Токен можна створити у налаштуваннях вашого аккаунту.</p>
    
    <h2>Доступні ендпоінти</h2>
    
    <h3>Отримання інформації</h3>
    <pre><code>GET /api</code></pre>
    <p>Повертає базову інформацію про користувача та принтери.</p>
    
    <h3>Початок друку</h3>
    <pre><code>POST /api/print-start</code></pre>
    <p>Задає початок задачі друку.</p>
    
    <h4>Параметри:</h4>
    <ul>
        <li><code>filename</code> (обов'язково) - назва файлу з інформацією про завдання та деталі</li>
        <li><code>printer_id</code> (опціонально) - ідентифікатор принтера.
            Якщо не вказати, то буде використано перший (або єдиний) принтер у профілі</li>
    </ul>
    
    <h4>Формат імені файлу:</h4>
    <p>Ім'я файлу повинно містити спеціальні маркери для ідентифікації завдань та деталей:</p>
    <ul>
        <li>Для друку певної деталі: <code>(pid_PART_ID(xКІЛЬКІСТЬ)_TASK_ID)</code></li>
        <li>Для друку всього набору: <code>(tid_TASK_ID(xКІЛЬКІСТЬ))</code></li>
    </ul>
    <p>Приклади:</p>
    <ul>
        <li><code>my_model_(pid_1_5).stl</code> - деталь ID 1 із завдання ID 5</li>
        <li><code>my_model_(pid_1(x2)_5).stl</code> - 2 екземпляри деталі ID 1 із завдання ID 5</li>
        <li><code>full_set_(tid_5).stl</code> - всі деталі із завдання ID 5</li>
        <li><code>full_set_(tid_5(x3)).stl</code> - 3 набори всіх деталей із завдання ID 5</li>
    </ul>

    <h4>Помилки:</h4>
    <ul>
        <li>Якщо вказаного завдання не існує, воно буде проігнороване, але інші завдання будуть оброблені.</li>
        <li>Якщо вказана деталь не існує, вона буде проігнорована, але інші деталі будуть оброблені.</li>
    </ul>

    <h3>Завершення друку</h3>
    <pre><code>POST /api/print-end</code></pre>
    <p>Відмічає задачу друку як завершену та оновлює лічильники.</p>
    
    <h4>Параметри:</h4>
    <ul>
        <li><code>filename</code> - назва файлу з інформацією про завдання (такий самий формат як і для початку друку).
            Можна опустити, якщо вам потрібно лише оновити витрати філаменту
        </li>
        <li><code>printer_id</code> (опціонально) - ідентифікатор принтера</li>
        <li><code>slots</code> (опціонально) - масив із даними про використання філаменту в слотах принтера.
            Слоти створюються у налаштуваннях принтера, там же до слоту прив'язується котушка філаменту.
        </li>
    </ul>
    
    <h4>Формат даних про слоти:</h4>
    <pre><code>{
    "slots": {
        "slot_name_1": 5.5,  // кількість витраченого філаменту в грамах
        "slot_name_2": 1.2
    }
}</code></pre>

    <h4>Помилки:</h4>
    <ul>
        <li>Якщо слот не існує, він буде проігнорований, але інші слоти будуть оброблені.</li>
        <li>Якщо слот не прив'язаний до котушки, він буде проігнорований, але інші слоти будуть оброблені.</li>
        <li>Якщо кількість витраченого філаменту не містить число, слот буде проігноровано, але інші слоти будуть оброблені.</li>
    </ul>

    <h3>Зупинка друку</h3>
    <pre><code>POST /api/print-stop</code></pre>
    <p>Скасовує поточні задачі друку для вказаного принтера.</p>
    
    <h4>Параметри:</h4>
    <ul>
        <li><code>printer_id</code> (обов'язково) - ідентифікатор принтера</li>
    </ul>
    
    <h2>Приклади запитів</h2>
    
    <h3>Початок друку</h3>
    <pre><code>curl -X POST https://your-domain.com/api/print-start \
    -H "Authorization: Bearer YOUR_API_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"filename": "part_(pid_1_5).stl", "printer_id": 1}'</code></pre>
    
    <h3>Завершення друку з даними про філамент</h3>
    <pre><code>curl -X POST https://your-domain.com/api/print-end \
    -H "Authorization: Bearer YOUR_API_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"filename": "part_(pid_1_5).stl", "printer_id": 1, "slots": {"AMS 1 Tray 1": 5.2, "AMS 1 Tray 3": 0.8}}'</code></pre>
    
    <h3>Зупинка друку</h3>
    <pre><code>curl -X POST https://your-domain.com/api/print-stop \
    -H "Authorization: Bearer YOUR_API_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"printer_id": 1}'</code></pre>
    
    <h2>Коди відповідей</h2>
    <ul>
        <li><strong>200 OK</strong> - Запит оброблено успішно</li>
        <li><strong>401 Unauthorized</strong> - Не вказано або невірний токен API</li>
        <li><strong>403 Forbidden</strong> - Недостатньо прав для виконання операції</li>
        <li><strong>422 Unprocessable Entity</strong> - Надані дані некоректні</li>
    </ul>
    
    <h2>Структура відповідей</h2>
    
    <h3>Відповідь при початку друку</h3>
    <p>При успішному запиті початку друку API повертає інформацію про стан завдань:</p>
    <pre><code>{
    "printer": {
        "id": 1,
        "name": "Назва принтера"
    },
    "tasks": {
        "success": true,
        "data": {
            "old": {
                "tasks": {
                    "1": {
                        "count_set_planned": 10,
                        "count_set_printed": 1,
                        "count_set_printing": 5,
                        "parts": {
                            "1": {
                                "part_task_id": 1,
                                "is_printing": true,
                                "count_per_set": 2,
                                "count_required": 20,
                                "count_printed": 2,
                                "count_printing": 10
                            }
                        }
                    }
                }
            }
        }
    }
}</code></pre>

    <h3>Відповідь при завершенні друку</h3>
    <p>При успішному запиті завершення друку API повертає інформацію про стан завдань та використаний філамент:</p>
    <pre><code>{
    "printer": {
        "id": 1,
        "name": "Назва принтера"
    },
    "tasks": {
        "success": true,
        "data": {
            "old": {
                "tasks": {
                    "1": {
                        "count_set_planned": 10,
                        "count_set_printed": 1,
                        "count_set_printing": 2,
                        "parts": {
                            "1": {
                                "part_task_id": 1,
                                "is_printing": true,
                                "count_per_set": 2,
                                "count_required": 20,
                                "count_printed": 2,
                                "count_printing": 5
                            }
                        }
                    }
                }
            },
            "new": {
                "tasks": {
                    "1": {
                        "count_set_planned": 10,
                        "count_set_printed": 3,
                        "count_set_printing": 0,
                        "parts": {
                            "1": {
                                "part_task_id": 1,
                                "count_per_set": 2,
                                "count_required": 20,
                                "count_printed": 7,
                                "count_printing": 0
                            }
                        }
                    }
                }
            }
        }
    },
    "slots": {
        "success": true,
        "errors": [],
        "data": {
            "input": {
                "AMS 1 Tray 1": 5.2,
                "AMS 1 Tray 3": 0.8
            },
            "old": {
                "AMS 1 Tray 1": {
                    "filament_spool_id": 1,
                    "weight_initial": 1000.0,
                    "weight_remaining": 800.5,
                    "weight_used": 199.5
                },
                "AMS 1 Tray 3": {
                    "filament_spool_id": 2,
                    "weight_initial": 1000.0,
                    "weight_remaining": 950.2,
                    "weight_used": 49.8
                }
            },
            "new": {
                "AMS 1 Tray 1": {
                    "filament_spool_id": 1,
                    "weight_initial": 1000.0,
                    "weight_remaining": 795.3,
                    "weight_used": 204.7
                },
                "AMS 1 Tray 3": {
                    "filament_spool_id": 2,
                    "weight_initial": 1000.0,
                    "weight_remaining": 949.4,
                    "weight_used": 50.6
                }
            }
        }
    }
}</code></pre>
    <div class="text-end">
    (дяка Claude Sonnet Thinking)
    </div>
</div>
@endsection
