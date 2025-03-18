## Print DB

Органайзер для роботи з 3D друком.

<h3>Загальна ідея:</h3>

* Організувати роботу з 3D принтером, щоб не забувати про те, що друкується, а також мати можливість контролювати процес друку.
* Вести облік частин, які надруковані, а також частин, які ще не надруковані.
* Вести облік завдань, які потрібно надрукувати, а також завдань, які вже надруковані.
* Автоматизувати процес друку та контролю за завданнями, щоб мінімізувати ручний ввід даних.

<h3>Реалізація (та її ідеї):</h3>

* Використовувати API для отримання даних про завдання та частини, які друкуються.
* Автоматично створювати завдання та частини, які потрібно надрукувати, на основі даних з API.
* Зв'язок між даними з API та даними в системі здійснюватиметься через назви файлів, які будуть містити інформацію про завдання та частини, які потрібно надрукувати.
* Зробити зручне керування завданнями та частинами, які потрібно надрукувати, через веб-інтерфейс, щоб можна було користуватися без API.
* Кожен користувач бачить лише свої завдання та частини, які він створив.
* У якості аватару використовується gravatar
* Стек: php8.3, laravel 12, mysql, redis, nginx
* Є файли для створення докер-контейнеру для розробки, але часто забуваю синхронізувати конфігурацію зі змінами у робочому контейнері, тому не гарантую їхню коректну роботу

<h3>Угода щодо понять:</h3>

* **Завдання** - це певна кількість комплектів, які потрібно надрукувати
* **Комплект** - це певна кількість різних **частин** (може бути одна **частина**, може бути різна кількість різних **частин** у комплекті)
* **Частина** - окрема модель, яка має свою назву та версію, та може входити до декількох **завдань**
* Статуси:
  * "**нова**" - завдання, яке ще не почало друкуватися. Це початковий статус створеного вручну завдання. Статус змініться автоматично на "**в процесі**" при находженні на API пов'язаної з цим завдання події
  * "**в процесі**" - завдання, яке вже почало друкуватися. Це початковий статус завдання, створенного автоматично. Статус зміниться автоматично на "**надруковано**" при досягненні потрібної кількості надрукованих частин (можливо, це буде опціонально)
  * "**надруковано**" - завдання, яке вже повністю надруковане. Цей статус не змінюється автоматично. Тако ж завдання з цим статусом все ще вважаються відкритими, і можуть приймати події з API. Цей статус потрібно вручну змінити на "**виконано**" після того, як завдання буде повністю надруковане та відправлено замовнику
  * "**виконано**" означає, що завдання повністю надруковано та відправлено замовнику

<h3>Події API:</h3>

* **початок друку** - для створення задач та частин, перевірка переданих даних. Сповіщення, якщо друк завершить завдання (чи частину завдання)
* **закінчення друку** - інкрементація надрукованих частин завдання. Сповіщення про завершення завдання (чи частини завдання)
* **отримання списку завдань та прив'язаних частин**
* **отримання вказаного завдання з прив'язаними частинами**

На API як мінімум передається назва файлу, з якого зчитуються дані. У планах ще айди принтеру (не обов'язково).

<h3>Назва файлу містить:</h3>

[tid_**9999**(x**1**)@**external_id**] або [tnm_**назва завдання**(x**1**)@**external_id**]
* tid_**ади завдання**
* tnm_**назва завдання**
* (x**кількість**) - скільки комплектів наразі друкується. За замовчуванням 1
* @**external_id** - зовнішній айді завдання для звуження пошуку або для подробиць при створенні нової задачі. За замовчуванням - null

* назва задачі не є унікальною, тому в розрізі користувача може бути кілька задач з однаковими назвами. При пошуку за назвою враховуються тільки незавершені задачі. Якщо такий пошук дає декілька результатів, то береться найновіше завдання
* в назві може бути тільки 1 завдання, але декілька частин
* при автоматичному створенні завдання, цільова кількість вказується 0
* при автоматичному створенні частин, комплектна кількість вказується 1

[pid_**9999**(x**1**)@**version**] або [pnm_**назва частини**(x**1**)@**version**]
* pid_**айді частини**
* pnm_**назва частини**
* (x**кількість**) - скільки частин наразі друкується. За замовчуванням 1
* @**version** - версія частини для звуження пошуку або для подробиць при створенні нової частини. За замовчуванням - v0. Це строка, може містити букви, цифри, символи. Крім пробілів. Якщо в назві частини є пробіли, то вони замінюються на "_".
* назва частини + version - є унікальною зв'язкою в розрізі користувача

[tid_**айді завдання**]
* шукається завдання з таким айді серед незавершених, якщо не знайдено - ігнорувати (сповіщення)
* якщо знайдено та не вказано кількість чи частини, вважати як 1 повний комплект
* якщо знайдено та вказані айді частин, яких немає в завданні - прикріпляти знайдені у системі (сповіщення) з 1 шт в комплекті, решту ігнорувати (сповіщення)
* якщо знайдено та вказані назви частин, яких немає в завданні - шукати за назвою та прикріпляти (сповіщення). Якщо назви не знайдено - створювати нові та прикріпляти (сповіщення)

[tnm_**назва завдання**]
* шукається завдання з такою назвою серед незавершених, якщо не знайдено - створюється нове (сповіщення)
* <sup>(1)</sup> якщо не вказано частини - створюється частина "айді завдання_main part" та прикріплюється (сповіщення)
* якщо вказано айді частин та знайдено - прикріпляти (сповіщення), інакше ігнорувати (сповіщення)

Якщо в завданні вказано кількість комплектів:
* якщо завдання знайдено, а в назві друку вказано частини, то кількість комплектів ігнорується (сповіщення)
* якщо завдання автоматично створено та не вказано частини, див (1)
* якщо завдяння автоматично створено та вказано частини, то їх кількість ділиться на кількість комплектів для обчислення комплектації і якщо результат не є ціле число - помилка (сповіщення). Якщо кількість частин не вказана, вважати по 1 шт в комплекті

Якщо не вказано завдання, але вказано частини:
* шукаються частини з такими айді або назвою серед незавершених завдань, якщо не знайдено - ігнорувати (сповіщення)
* автоматичного створення немає, тому що немає дло чого прив'язувати частини
