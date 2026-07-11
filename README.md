# BodyTrack

Веб-приложение для учёта показаний весов-анализатора Geepas GBS46505UK:
вес, % жира, % воды, мышцы, кости, висцеральный жир, ИМТ, метаболизм.
Считает нормы по полу и возрасту (ВОЗ, таблицы BIA), показывает динамику
на графиках и даёт практические рекомендации по правилам.

Стек: Laravel 13 + Fortify, SQLite, Tailwind CSS v4 (standalone CLI, без
Node.js), Chart.js (локально). Интерфейс: русский и английский.

## Запуск (локально, OSPanel)

PHP 8.4 и Composer лежат в `C:\OSPanel\modules\PHP-8.4\PHP`.

```powershell
$env:Path = "C:\OSPanel\modules\PHP-8.4\PHP;" + $env:Path

# почтовый перехватчик (подтверждение регистрации), UI: http://127.0.0.1:8025
Start-Process C:\OSPanel\modules\Mailpit\mailpit.exe -ArgumentList "--smtp","127.0.0.1:1025","--listen","127.0.0.1:8025"

# дев-сервер
php artisan serve --port=8000
```

Приложение: http://localhost:8000

## Запуск в Docker

Нужен установленный [Docker Desktop](https://www.docker.com/products/docker-desktop/)
(на Windows — с включённым WSL2).

```powershell
docker compose up -d --build
```

- Приложение: http://localhost:8000
- Почта (Mailpit UI): http://localhost:8025

Что внутри: контейнер `app` (PHP 8.4 + Apache, миграции выполняются при
старте автоматически) и контейнер `mailpit` для перехвата писем. База
SQLite хранится на томе `dbdata` и переживает пересборку образа. Ключ
приложения и прочие настройки берутся из `.env`, но хосты почты и путь
к базе переопределены в `compose.yaml` под докер-сеть.

Остановить: `docker compose down` (с удалением базы — `docker compose down -v`).

После правок кода пересоберите образ: `docker compose up -d --build`.

## Сборка стилей

После правок Blade-шаблонов или `resources/css/app.css`:

```powershell
.\build-css.bat
```

Использует автономный Tailwind CLI из `..\tools\tailwindcss.exe` — Node.js
не нужен.

## Тесты

```powershell
php artisan test
```

## Структура

- `app/Services/BodyNorms.php` — нормы показателей (пол, возраст) и оценка
- `app/Services/RecommendationEngine.php` — правила рекомендаций по трендам
- `app/Http/Controllers/` — Dashboard, Measurements (+CSV-экспорт), Charts, Profile, Locale
- `lang/ru`, `lang/en` — переводы интерфейса

## Продакшен-заметки

- Заменить SQLite на MySQL/PostgreSQL (одна строка в `.env`)
- Настроить реальный SMTP вместо Mailpit (`MAIL_*` в `.env`)
- `APP_ENV=production`, `APP_DEBUG=false`, `php artisan config:cache`
