# Описание функционала системы управления клубами мафии

Документ описывает реализацию основных функциональных модулей приложения.

---

## 1. Регистрация и аутентификация

### Регистрация нового пользователя

**Контроллер**: `AuthController::register()` (строка 124)
**Маршрут**: `POST /register`
**View**: `resources/views/auth/register.blade.php`

**Процесс регистрации:**

1. Валидация данных:
   - `name` — обязательно, до 255 символов
   - `email` — обязательно, уникальный, валидный email
   - `password` — обязательно, минимум 6 символов, с подтверждением

2. Автоактивация для домена `@clm.org`:
   - Если email заканчивается на `@clm.org`, аккаунт активируется сразу без подтверждения
   - Для остальных доменов генерируется токен подтверждения (32 символа)

3. Отправка email с подтверждением:
   - Шаблон: `resources/views/emails/welcome.blade.php`
   - Письмо содержит ссылку для подтверждения: `/verify-email/{token}`

4. После регистрации пользователь перенаправляется на `/login` с сообщением о необходимости подтвердить email

### Подтверждение email

**Маршрут**: `GET /verify-email/{token}` (routes/web.php:34)

**Процесс:**
1. Поиск пользователя по токену `email_verification_token`
2. Обновление полей:
   - `is_active = true`
   - `email_verified_at = now()`
   - `email_verification_token = null`
3. Автоматический вход в систему через `Auth::login($user)`
4. Перенаправление на `/dashboard`

### Вход в систему

**Контроллер**: `AuthController::login()` (строка 23)
**Маршрут**: `POST /login`

**Процесс:**
1. Проверка существования пользователя и активности аккаунта (`is_active = true`)
2. Если аккаунт не активирован — отображение ошибки "Please verify your email"
3. Попытка аутентификации через `Auth::attempt($credentials)`
4. При успехе — регенерация сессии и перенаправление на `/dashboard`

### Восстановление пароля

**Контроллеры**:
- `AuthController::forgotPasswordForm()` — форма запроса сброса
- `AuthController::sendPasswordResetEmail()` — отправка письма
- `AuthController::resetPassword()` — сброс пароля

**Маршруты**:
- `GET /forgot-password` — форма
- `POST /forgot-password` — отправка email
- `GET /reset-password/{token}` — форма сброса
- `POST /reset-password` — обновление пароля

**Процесс:**
1. Пользователь вводит email
2. Генерируется токен (64 символа)
3. Токен сохраняется в таблицу `password_reset_tokens` (хешированный)
4. Отправляется email с ссылкой: `/reset-password/{token}?email={email}`
5. При сбросе проверяется валидность токена через `Hash::check()`
6. Пароль обновляется, токен удаляется

### Вход через Google

**Контроллеры**:
- `AuthController::redirectToGoogle()` — редирект на Google OAuth
- `AuthController::handleGoogleCallback()` — обработка ответа

**Маршруты**:
- `GET /auth/google`
- `GET /auth/google/callback`

**Процесс:**
1. Использование Laravel Socialite для OAuth
2. Получение данных пользователя от Google
3. Поиск существующего пользователя по email
4. Если пользователь существует:
   - Обновление `google_id` если отсутствует
5. Если нет — создание нового пользователя:
   - `email_verified_at = now()` (Google верифицирует email)
   - `is_active = true`
   - Генерация случайного пароля
6. Автоматический вход и перенаправление на профиль

### Вход через Facebook

**Контроллеры**:
- `AuthController::redirectToFacebook()` — редирект на Facebook OAuth
- `AuthController::handleFacebookCallback()` — обработка ответа

**Маршруты**:
- `GET /auth/facebook`
- `GET /auth/facebook/callback`

**Процесс аналогичен Google**, дополнительно:
- Запрашиваются права `email` и `public_profile`
- Сохраняется `facebook_id` и аватар пользователя
- Поиск по `facebook_id` или `email`

### Выход

**Маршруты**:
- `GET /logout` — форма подтверждения выхода
- `POST /logout` — выполнение выхода

---

## 2. Панель администратора

### Главная панель (Dashboard)

**Контроллер**: `SuperAdminController::dashboard()` (строка 54)
**Маршрут**: `GET /dashboard` (middleware: `auth`)
**View**: `resources/views/dashboard.blade.php`

**Структура меню** (SuperAdminController::$sidebarMenu):

1. **Роли** → `/roles`
2. **Пользователи** → `/users`
3. **Клубы** → `/manage/clubs`
4. **Страны** → `/countries`
5. **Города** → `/cities`
6. **Типы запросов** → `/request-types`

Меню реализовано как массив с обработчиками JavaScript для переходов.

### Управление пользователями

**Контроллер**: `UserController` + `PlayerPagesController`
**Маршруты**:
- `GET /users` — список пользователей
- `GET /users/management` — расширенная панель управления (PlayerPagesController:227)
- `GET /users/{user}` — просмотр
- `GET /users/{user}/edit` — редактирование
- `PUT /users/{user}` — обновление
- `DELETE /users/{user}` — удаление

**Функции**:
- Пагинация (10 или 30 записей на страницу)
- Просмотр профилей игроков
- Редактирование данных: имя, email, страна, город, клуб, аватар
- Назначение глобальных ролей (через GlobalRoleController)

### Управление странами

**Контроллер**: `CountryController`
**Маршрут**: `Route::resource('countries', CountryController::class)` (middleware: `auth`)

Стандартный CRUD для справочника стран.

### Управление городами

**Контроллер**: `CityController`
**Маршрут**: `Route::resource('cities', CityController::class)` (middleware: `auth`)

Стандартный CRUD для справочника городов с привязкой к странам.

### Управление типами запросов

**Контроллер**: `RequestTypeController`
**Маршрут**: `Route::resource('request-types', RequestTypeController::class)` (middleware: `auth`)

Управление типами заявок и запросов в системе.

---

## 3. Клубы

### Просмотр списка клубов

**Контроллер**: `ClubController::index()` (строка 14)
**Маршрут**: `GET /clubs`
**View**: `resources/views/clubs/index.blade.php`

- Список клубов с владельцами (eager loading)
- Пагинация по 10 записей
- Сортировка по дате создания (latest)

### Создание клуба

**Контроллер**: `ClubController::create()` и `store()` (строки 23, 41)
**Маршруты**:
- `GET /clubs/create`
- `POST /clubs`

**Валидация**:
- `name` — обязательно, до 255 символов
- `email` — опционально, валидный email
- `country_id`, `city_id` — опционально, должны существовать
- `avatar`, `logo`, `banner` — изображения с ограничениями:
  - Форматы: jpeg, png, jpg, gif
  - Размер: до 2MB
  - `avatar` должен быть квадратным (ratio 1:1)

**Обработка изображений**:
- Типы изображений определены в `Club::IMG_TYPES`:
  - `avatar`: 300x300px → `storage/club/avatar/`
  - `logo`: 500x500px → `storage/club/logo/`
  - `banner`: 1500x500px → `storage/club/banner/`
- Обработка через Intervention Image (метод `Club::saveImg()`)

**Автоматические поля**:
- `owner_id` — текущий аутентифицированный пользователь

### Просмотр клуба

**Контроллер**: `ClubController::show()` (строка 68)
**Маршрут**: `GET /clubs/{club}`
**View**: `resources/views/clubs/show.blade.php`

**Функционал**:
- Отображение всей информации о клубе
- Вкладки (передаются через query параметр `?tab=tournaments`)
- Flash-сохранение активной вкладки в сессии

### Редактирование клуба

**Контроллер**: `ClubController::edit()` и `update()` (строки 80, 99)
**Маршруты**:
- `GET /clubs/{club}/edit`
- `PUT /clubs/{club}`

**Авторизация**:
- Policy `ClubPolicy::manage_club` (ClubController:84)
- Только владелец может редактировать (проверка `owner_id` на строке 101)

### Удаление клуба

**Контроллер**: `ClubController::destroy()` (строка 133)
**Маршрут**: `DELETE /clubs/{club}`

### Система заявок на вступление

**Контроллер**: `ClubMembershipController`
**Модели**: `ClubRequest`, `ClubMember`

#### Подача заявки

**Метод**: `requestJoin()` (строка 13)
**Маршрут**: `POST /clubs/{club}/join` (middleware: `auth`)

**Процесс**:
1. Создание записи в `club_requests`:
   - `user_id` — текущий пользователь
   - `club_id` — клуб
   - `message` — опциональное сообщение
   - `status = 'pending'`

#### Принятие заявки

**Метод**: `approveRequest()` (строка 28)
**Маршрут**: `POST /join-requests/{joinRequest}/approve`

**Авторизация**: Policy `ClubRequestPolicy::approve`

**Процесс**:
1. Обновление статуса заявки: `status = 'approved'`
2. Создание записи в `club_members` (или нахождение существующей)
3. Пользователь становится участником клуба

#### Отклонение заявки

**Метод**: `declineRequest()` (строка 42)
**Маршрут**: `POST /join-requests/{joinRequest}/decline`

**Процесс**:
1. Обновление заявки:
   - `status = 'rejected'`
   - `declined_at = now()`
2. В коде есть комментарий о возможности отправки уведомления

#### Выход из клуба

**Метод**: `leave()` (строка 58)
**Маршрут**: `POST /clubs/{club}/leave`

**Процесс**:
- Удаление записи из `club_members` по `user_id` и `club_id`

---

## 4. Роли и права доступа

### Архитектура системы

**Модели**:
- `Role` — роли (глобальные и клубные)
- `Permission` — разрешения
- `RolePermission` — связь ролей и разрешений
- `UserRole` — назначение ролей пользователям

**Типы ролей**:
- `scope = 'global'` — глобальные роли (системные администраторы)
- `scope = 'club'` — роли внутри клуба

### Управление ролями клуба

**Контроллер**: `RoleController` (app/Http/Controllers/RoleController.php)
**Маршруты**: `Route::prefix('clubs/{club}/roles')`

#### Список ролей клуба

**Метод**: `index()` (строка 20)
**Маршрут**: `GET /clubs/{club}/roles`

**Авторизация**: `manage_club_members` permission

**Возвращает**:
- Роли клуба с разрешениями (eager loading)
- Глобальные роли
- Список всех доступных разрешений (`Permission::all()`)

#### Создание роли

**Метод**: `create()` и `store()` (строки 57, 121)
**Маршруты**:
- `GET /clubs/{club}/roles/create`
- `POST /clubs/{club}/roles`

**Валидация**:
- `name` — обязательно
- `description` — опционально
- `permissions` — массив ID разрешений

**Процесс**:
1. Создание роли с автоматическим `slug` (через `Str::slug()`)
2. Привязка к клубу через `$club->roles()->create()`
3. Установка `scope = 'club'`
4. Прикрепление разрешений через `$role->permissions()->attach()`

#### Редактирование роли

**Метод**: `edit()` и `update()` (строки 81, 93)
**Маршруты**:
- `GET /clubs/{club}/roles/{role}/edit`
- `PUT /clubs/{club}/roles/{role}`

**Авторизация**: `manage_club_members`

#### Назначение роли пользователю

**Метод**: `assignRole()` (строка 148)
**Маршрут**: `POST /clubs/{club}/roles/assign`

**Параметры**:
- `user_id` — ID пользователя
- `role_id` — ID роли

**Проверки**:
1. Роль принадлежит клубу или является глобальной
2. Если роль клубная — `role->club_id` должен совпадать

**Процесс**:
- Использование `syncWithoutDetaching()` для избежания дублирования
- Сохранение `club_id` в pivot-таблице для клубных ролей

#### Отзыв роли

**Метод**: `revokeRole()` (строка 173)
**Маршрут**: `DELETE /clubs/{club}/users/{user}/roles/{role}`

**Процесс**:
- Проверка принадлежности роли клубу
- `$user->roles()->detach($role->id)`

#### Обновление разрешений роли

**Метод**: `updatePermissions()` (строка 188)
**Маршрут**: `PUT /clubs/{club}/roles/{role}/permissions`

**Ограничения**:
- Нельзя изменить глобальные роли
- Роль должна принадлежать клубу

**Процесс**:
- Синхронизация разрешений через `$role->permissions()->sync()`

### Глобальные роли

**Контроллер**: `GlobalRoleController`
**Маршруты**:
- `GET /users/{user}/role` — форма назначения глобальной роли
- `POST /users/{user}/role` — назначение
- `POST /users/{user}/role/retract` — отзыв
- `GET /users/{user}/club-role/{club}` — форма назначения клубной роли
- `POST /users/{user}/role/{club}` — назначение клубной роли

### Проверка прав

**Middleware**: `CheckPermission` (app/Http/Middleware/CheckPermission.php)

**Использование**:
```php
Route::middleware(['auth', 'permission:manage_club,club_id'])
```

**Процесс**:
1. Получение текущего пользователя
2. Извлечение `club_id` из параметров маршрута (если указан)
3. Вызов `User::hasPermission($permission, $clubId)`
4. Если прав нет — abort(403)

---

## 5. Турниры

### Модель турнира

**Модель**: `Tournament` (app/Models/Tournament.php)

**Фазы турнира** (константа `PHASES`):
- `draft` — Черновик
- `registration` — Регистрация
- `qualifying` — Квалификация
- `finals` — Финалы
- `in_progress` — В процессе
- `closed` — Закрыт
- `cancelled` — Отменен
- `finished` — Завершен

**Поля**:
- Основные: `name`, `club_id`, `date_start`, `date_end`, `location`
- Квоты: `quota`, `players_quota`, `games_quota`
- Финансы: `prize`, `participation_fee`
- Изображения: `logo`, `banner`, `stream_banner`
- Метаданные: `duration`, `phase`, `description`

### Список турниров

**Контроллер**: `TournamentController::index()` (строка 37)
**Маршрут**: `GET /tournaments`

**Функционал**:
- Список турниров с клубами (eager loading)
- Фильтрация по фазам (вероятно в представлении)

### Создание турнира

**Контроллер**: `create()` и `store()` (строки 43, 68)
**Маршруты**:
- `GET /clubs/{club}/tournaments/create`
- `POST /clubs/{club}/tournaments`

**Авторизация**: Policy `create_tournament` для клуба

**Валидация**:
- Даты: `date_end` должна быть >= `date_start`
- Изображения:
  - `logo`: квадрат (1:1), до 2MB
  - `banner`: широкий (3:1), до 5MB
  - `stream_banner`: 16:9, до 5MB
- Все числовые квоты должны быть >= 1

**Автоматика**:
- По умолчанию `phase = 'draft'`
- Даты по умолчанию: следующая пятница - воскресенье
- `duration` рассчитывается автоматически: `diffInDays() + 1`
- `club_id` устанавливается из маршрута

**Обработка изображений**:
- Сохранение в `storage/app/public/tournament-{type}s/`

### Просмотр турнира

**Контроллер**: `show()` (строка 96)
**Маршрут**: `GET /clubs/{club}/tournaments/{tournament}`

**Авторизация**: Policy `manage_tournament`

**Функционал**:
- Поддержка AJAX-запросов (через заголовок `X-Ajax-Request`)
- Выбор layout: `layouts.ajax` или `layouts.app`

### Редактирование турнира

**Контроллер**: `edit()` и `update()` (строки 113, 130)
**Маршруты**:
- `GET /clubs/{club}/tournaments/{tournament}/edit`
- `PUT /clubs/{club}/tournaments/{tournament}`

**Авторизация**: Policy `manage_tournament`

**Перенаправление**:
- После обновления → страница клуба с вкладкой "tournaments"
- Flash-сообщение: "Tournament updated!"

### Удаление турнира

**Контроллер**: `destroy()` (строка 170)
**Маршрут**: `DELETE /clubs/{club}/tournaments/{tournament}`

### Участники турнира

**Контроллер**: `TournamentParticipantController`
**Модель**: `TournamentParticipant` (pivot-таблица many-to-many)

#### Список участников

**Маршрут**: `GET /tournaments/{tournament}/participants`

#### Добавление участника

**Маршрут**: `POST /tournaments/{tournament}/participants`

**Валидация**:
- `user_id` должен быть уникальным для турнира
- Проверка через составной unique constraint

**Процесс**:
- `$tournament->participants()->attach($user_id)`

#### Удаление участника

**Маршрут**: `DELETE /tournaments/{tournament}/participants/{user}`

**Процесс**:
- `$tournament->participants()->detach($user_id)`

### Судьи турнира

**Модель**: `TournamentJudges`

Отдельная сущность для управления судьями турнира (контроллер не показан в коде).

---

## 6. Игры

### Модель игры

**Модель**: `Game` (app/Models/Game.php)

**Роли в игре** (константа `ROLES`):
- `mafia` — Мафия
- `sheriff` — Шериф
- `citizen` — Мирный
- `don` — Дон

### State Machine игры

**Константа**: `PHASES_ORDER` (строки 21-164)

Сложная машина состояний с переходами между фазами.

#### Подготовка к игре

1. **SHUFFLE-SLOTS** (рассадка)
   - Фаза: `shuffle-slots`
   - День: 0

2. **SHUFFLE-ROLES** (раздача ролей)
   - Фаза: `shuffle-roles`
   - День: 0

#### Ночь 0

3. **NIGHT-CAHOOT** (договорка мафии)
   - Фаза: `night`, подфаза: `cahoot`
   - Таймер: 60 сек

4. **SHERIFF-SIGN** (знак шерифу)
   - Подфаза: `sheriff-sign`
   - Таймер: 10 сек

5. **FREE** (свободная посадка)
   - Подфаза: `free`
   - Таймер: 20 сек

6. **SHOOTING** (стрельба мафии)
   - Подфаза: `shooting`
   - Таймер: 20 сек

#### Ночь 1+

7. **DON-CHECK** (проверка дона)
   - Подфаза: `don-check`
   - Таймер: 10 сек

8. **SHERIFF-CHECK** (проверка шерифа)
   - Подфаза: `sheriff-check`
   - Таймер: 10 сек

9. **FIRST-KILL** (первый убитый)
   - Подфаза: `first-kill`
   - Таймер: 10 сек

10. **BEST-GUESS** (лучший ход)
    - Подфаза: `best-guess`
    - Таймер: 10 сек

#### День

11. **LAST-SPEECH-KILLED** (завещание убитого)
    - Фаза: `day`, подфаза: `last-speech-killed`
    - Таймер: 60 сек

12. **PROTOCOL-COLOR** (цвет в протоколе)
    - Подфаза: `protocol-color`

13. **DAY-SPEECH** (дневные выступления)
    - Подфаза: `speaker`
    - Таймер: 60 сек

14. **VOTING-ROUND** (голосование)
    - Подфаза: `voting-round`
    - Таймер: 60 сек

15. **LAST-SPEECH-VOTED** (завещание выбывшего)
    - Подфаза: `last-speech-voted`
    - Таймер: 60 сек

#### Завершение

16. **SCORE** (подсчет результатов)
    - Фаза: `game-over`, подфаза: `score`
    - Таймер: 0 (без ограничений)

17. **SCORE-SAVE** (сохранение результатов)
    - Фаза: `score`, подфаза: `save`

18. **FINISHED** (игра завершена)
    - Фаза: `finished`

### Управление игрой

**Контроллер**: `GameController`
**Связь**: Игры привязаны к `Event` (события турнира)

#### Маршруты

- `GET /events/{event}/games` — список игр события
- `POST /events/{event}/games` — создание игры
- `GET /events/{event}/games/{game}` — просмотр
- `PUT /events/{event}/games/{game}` — обновление
- `DELETE /events/{event}/games/{game}` — удаление

#### Создание игры

**Валидация**:
- `name` — название игры
- `date` — дата проведения
- `start`, `end` — время начала/окончания
- `props` — JSON с настройками
- `protocol` — JSON с протоколом игры

#### Функционал ведущего

Из модели `Game` видно, что ведущий может:

1. **Управлять фазами**:
   - Переключение между фазами согласно `PHASES_ORDER`
   - Каждая фаза имеет таймер и описание

2. **Управлять игроками**:
   - Назначение ролей
   - Отслеживание статуса (жив/мертв)
   - Выдача предупреждений (фолов)

3. **Управлять голосованием**:
   - Номинация игроков
   - Подсчет голосов
   - Фиксация результатов

4. **Вести протокол**:
   - Сохранение действий в JSON (`protocol`)
   - Отметки цветов в протоколе
   - Фиксация убийств, проверок, голосований

5. **Трансляция**:
   - Настройки трансляции в `props` (JSON)

### Участники игры

**Модель**: `GameParticipant`

**Связь**: many-to-many между `Game` и `User`

**Данные участника**:
- Роль в игре (из `Game::ROLES`)
- Порядковый номер (слот)
- Статус (жив/мертв)
- Предупреждения (фолы)

---

## 7. Каталог игроков

### Список игроков

**Контроллер**: `PlayerPagesController::index()` (строка 91)
**Маршрут**: `GET /players`
**View**: `resources/views/players/index.blade.php`

**Функционал**:
- Список всех зарегистрированных пользователей
- Пагинация: 30 записей на страницу
- Сортировка по дате регистрации (latest)
- Колонки:
  - Рейтинг (default: 0)
  - Дополнительные поля (определяются в view)

### Профиль игрока

**Контроллер**: `PlayerPagesController::show()` (строка 118)
**Маршрут**: `GET /players/{player}`
**View**: `resources/views/players/show.blade.php`

**Функционал**:
- Отображение полной информации игрока
- Вкладки (через query параметр `?tab=stats`):
  - **stats** — Игровая статистика
  - **clubs** — Клубы игрока
  - **friends** — Друзья
  - **tournaments** — Турниры
  - **games** — Игры

**Sidebar menu** (PlayerPagesController::$sidebarMenu, строка 19):
- Иконки Material Symbols
- Обработчики для переключения вкладок
- Активная вкладка сохраняется в сессии

**Метод**: `User::getPlayerInfo()` (вызывается на строке 125)

Возвращает агрегированную информацию:
- Количество игр
- Количество турниров
- Клубы
- Статистика побед/поражений (предположительно)

### Редактирование профиля

**Контроллер**: `PlayerPagesController::edit()` и `update()` (строки 141, 183)
**Маршруты**:
- `GET /users/{user}/edit`
- `PUT /users/{user}`

**Валидация**:
- `name` — до 64 символов
- `email` — уникальный (кроме текущего пользователя)
- `first_name`, `last_name` — до 32 символов
- `country_id`, `city_id`, `club_id` — опционально, должны существовать
- `avatar` — изображение, до 2MB

**Обработка аватара**:
- Метод `User::saveAvatar($request, $user)`
- Обработка через Intervention Image

**Особенность**:
- `club_id = 0` или `'null'` преобразуется в `null`
- Пароль обновляется только если заполнен (`filled()`)

### Личный кабинет

**Контроллер**: `PlayerPagesController::profile()` (строка 162)
**Маршрут**: `GET /users/profile` (middleware: `auth`)

**Функционал**:
- Редактирование собственного профиля
- Аналогичен редактированию пользователя
- Использует тот же view: `users.show`

---

## Технические детали

### Авторизация

**Policies**:
- `ClubPolicy` — управление клубом, участниками
- `ClubRequestPolicy` — обработка заявок
- `RolePolicy` — управление ролями

**Middleware**:
- `auth` — требует аутентификации
- `CheckPermission` — проверка разрешений

**Метод проверки прав**:
```php
$user->hasPermission($permission, $clubId = null)
```

### Обработка изображений

**Библиотека**: Intervention Image v3

**Модели с обработкой**:
- `User` — avatar
- `Club` — avatar, logo, banner
- `Tournament` — logo, banner, stream_banner

**Процесс**:
1. Валидация формата и размера
2. Изменение размера согласно константам
3. Сохранение в `storage/app/public/{dir}/`
4. Возврат пути для записи в БД

### AJAX-поддержка

Некоторые контроллеры поддерживают AJAX:
- Проверка заголовка `X-Ajax-Request`
- Выбор layout: `layouts.ajax` vs `layouts.app`
- Турниры, игры

### Локализация

**Middleware**: `SetLocale`
**Доступные языки**: `en`, `ru`
**Маршрут**: `GET /set-locale/{locale}`

**Процесс**:
- Сохранение выбора в сессии
- Автоматическое применение при каждом запросе

### Email-уведомления

**Шаблоны** (resources/views/emails/):
- `welcome.blade.php` — приветствие + подтверждение email
- `reset-password.blade.php` — сброс пароля

**Отправка**:
- В dev-режиме (`MAIL_MAILER=log`) письма записываются в лог
- В production — через настроенный драйвер (Mailgun поддерживается)

### Состояние сессии

**Flash-данные**:
- `tab` — активная вкладка (клубы, турниры, игроки)
- `success` — сообщения об успешных операциях
- `clm` — кастомные сообщения приложения
- `locale` — выбранный язык

### Структура БД

**Связи**:
- User ↔ Club (many-to-many через `club_members`)
- User ↔ Role (many-to-many через `user_roles` с `club_id`)
- Role ↔ Permission (many-to-many через `role_permissions`)
- Club ↔ Tournament (one-to-many)
- Tournament ↔ User (many-to-many через `tournament_participants`)
- Tournament ↔ Event (one-to-many)
- Event ↔ Game (one-to-many)
- Game ↔ User (many-to-many через `game_participants`)

### Особенности маршрутизации

**Группировка**:
- Все защищенные маршруты в `Route::middleware('auth')`
- Вложенные маршруты для клубов: `/clubs/{club}/roles`, `/clubs/{club}/tournaments`
- RESTful resources для основных сущностей

**Именование маршрутов**:
- Стандартное Laravel: `clubs.index`, `clubs.show`, etc.
- Вложенные: `clubs.roles.index`, `tournaments.participants.index`