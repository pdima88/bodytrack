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
