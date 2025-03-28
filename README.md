<a href="https://www.buymeacoffee.com/realshade" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a><br>
<a href="https://send.monobank.ua/jar/uUd4ZGseJ" target="_blank">Банка</a>

## Print DB

Органайзер для роботи з 3D друком.

https://print-db.realshade.me<br>
Наразі реєстрація вільна, без очікування підтвердження адміністратором<br>
Проєкт у розробці, тому можуть бути баги, а функції реалізовуватимуться поступово.

<h3>Що працює:</h3>
* Реестр завдань 
* Реестр частин
* Реестр принтерів
* API події "перед друком" та "після друку"
* Автоматизація обліку надрукованого при використанні API
* Інструмент перевірки назви файлу на валідність

<h3>Загальна ідея:</h3>

* Організувати роботу з 3D принтером в плані ведення обліку друку.
* Облік завдань та частин, які потрібно надрукувати та які вже надруковані.
* Автоматизувати процес підрахунку кількості надрукованих частин, а також автоматично створювати завдання та частини, які потрібно надрукувати, на основі даних з API.

<h3>Реалізація (та її ідеї):</h3>

* Використовувати API для отримання даних про завдання та частини, які друкуються.
* ? Автоматично створювати завдання та частини, які потрібно надрукувати, на основі даних з API.
* Зв'язок між даними з API та даними в системі здійснюватиметься через назви файлів, які будуть містити інформацію про завдання та частини, які потрібно надрукувати.
* Зробити зручне керування завданнями та частинами, які потрібно надрукувати, через веб-інтерфейс, щоб можна було користуватися без API.
* Звісно кожен користувач бачить лише свої завдання та частини, які він створив.
* У якості аватару використовується gravatar
* Стек: php8.3, laravel 12, mysql, redis, nginx. Bootstrap 5, SweetAlert2

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

(tid_**9999**(x**1**))
* tid_**ади завдання**
* (x**кількість**) - скільки комплектів наразі друкується. За замовчуванням 1
* шукається завдання з таким айді серед незавершених, якщо не знайдено - помилка (сповіщення)
* якщо знайдено та не вказано кількість, вважати як 1 повний комплект

(pid_**8888**(x**1**)_**9999**)
* pid_**айді частини**
* (x**кількість**) - скільки екземплярів частин наразі друкується. За замовчуванням 1
* @**айди завдання**
* шукається частина зі завданням з таким айді серед незавершених, якщо не знайдено завдання, або частина не належить завданню - помилка (сповіщення)
