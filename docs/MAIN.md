## Проект

Система управления клубами мафии (Mafia Club Management System) — Laravel 12 приложение для организации турниров, игр и клубов по настольной игре Мафия.

## Основные команды

### Разработка
```bash
# Запуск полного dev-окружения (сервер + очереди + логи + vite)
composer dev

# Или запуск отдельных сервисов:
php artisan serve                    # Запуск dev-сервера
php artisan queue:listen --tries=1   # Запуск обработки очередей
php artisan pail --timeout=0         # Просмотр логов в реальном времени
npm run dev                          # Запуск Vite для сборки фронтенда
```

### Тестирование
```bash
composer test                        # Запуск всех тестов
php artisan test                     # Запуск PHPUnit тестов
php artisan test --filter ИмяТеста  # Запуск конкретного теста
```

### База данных
```bash
php artisan migrate                  # Применить миграции
php artisan migrate:fresh --seed     # Пересоздать БД с сидами
php artisan db:seed                  # Запустить сиды
```

### Сборка и линтинг
```bash
npm run build                        # Production-сборка фронтенда
php artisan pint                     # Форматирование кода (Laravel Pint)
```

### Другие полезные команды
```bash
php artisan tinker                   # REPL для взаимодействия с приложением
php artisan route:list               # Список всех маршрутов
php artisan config:clear             # Очистка кеша конфигурации
php artisan cache:clear              # Очистка кеша приложения
```

## Архитектура

### Доменная модель

Приложение построено вокруг следующих основных сущностей:

**Клубы и пользователи:**
- `User` — игроки и администраторы системы
- `Club` — клубы мафии с владельцем, участниками и настройками
- `ClubMember` — участники клуба с ролями внутри клуба
- `ClubRequest` — заявки на вступление в клуб
- `Country`, `City` — географическая привязка клубов

**Турниры и игры:**
- `Tournament` — турниры с фазами (draft, registration, qualifying, finals, etc.)
- `TournamentParticipant` — участники турнира
- `TournamentJudges` — судьи турнира
- `Game` — конкретные игры в мафию с детальным state machine
- `GameParticipant` — участники игры с ролями (mafia, sheriff, citizen, don)
- `Event` — события в турнирах

**Права и роли:**
- `Role` — роли внутри клуба (owner, admin, moderator, etc.)
- `Permission` — разрешения для действий
- `RolePermission` — связь ролей и разрешений
- `UserRole` — глобальные роли пользователей в системе

### Система фаз игры

Модель `Game` содержит сложную state machine для управления фазами игры мафии:
- Фазы: shuffle-slots, shuffle-roles, night (с подфазами cahoot, sheriff-sign), day (с подфазами speaking, voting, last-words)
- Константа `PHASES_ORDER` описывает граф переходов между фазами
- Роли: mafia, sheriff, citizen, don

### Middleware

- `CheckPermission` — проверка прав доступа на уровне клуба или глобальных
- `SetLocale` — установка локали приложения
- `VerifyTelegramBot` — проверка запросов от Telegram бота

### Policies

Авторизация через Laravel Policies для:
- `ClubPolicy` — права на управление клубом
- `ClubRequestPolicy` — права на обработку заявок
- `RolePolicy` — права на управление ролями

### Структура маршрутов

Все маршруты определены в `routes/web.php`:
- Публичные: `/login`, `/register`, `/verify-email/{token}`
- Защищенные: dashboard, управление клубами, турнирами, играми
- API для Telegram бота (вероятно через VerifyTelegramBot middleware)

### Изображения

Модели `User` и `Club` используют Intervention Image для обработки загружаемых изображений:
- `Club::IMG_TYPES` определяет типы изображений (avatar, logo, banner) с размерами
- Изображения хранятся в `storage/app/public/{тип}/`

### База данных

- По умолчанию используется SQLite (`DB_CONNECTION=sqlite`)
- Очереди и кеш хранятся в БД (`QUEUE_CONNECTION=database`, `CACHE_STORE=database`)
- Сессии также в БД (`SESSION_DRIVER=database`)

### Фронтенд

- Vite для сборки
- Tailwind CSS 4.0
- Blade-шаблоны в `resources/views/`
- JS/CSS в `resources/js/` и `resources/css/`

## Особенности разработки

### Права доступа

Права проверяются через метод `User::hasPermission($permission, $clubId = null)`:
- Глобальные права (без $clubId) для системных администраторов
- Права на уровне клуба для владельцев/админов клуба

### Работа с изображениями

При загрузке изображений для клуба или пользователя используй константы из моделей для размеров и директорий.

### Локализация

- Язык устанавливается через middleware `SetLocale`
- Переводы в `lang/`
- Используется пакет `laravel-lang/lang` для готовых переводов

### Email

В dev-окружении (`MAIL_MAILER=log`) письма пишутся в лог, не отправляются.

### Очереди

Фоновые задачи через Laravel Queues с драйвером `database`. При разработке запускай `php artisan queue:listen --tries=1`.
