Пошаговая инструкция по запуску

1. # Перейдите в папку XAMPP
cd C:\xampp\htdocs

2. # Склонируйте репозиторий
git clone

3. # Перейдите в папку проекта
cd contest-platform

4. # Установка всех необходимых пакетов
composer install

5. # Для Windows (cmd)
copy .env.example .env

# Для Git Bash
cp .env.example .env

6. # Сгенерируйте ключ приложения
php artisan key:generate

7. # Настройка .env

APP_NAME="Contest Platform"
APP_ENV=local
APP_KEY=сгенерирован автоматически
APP_DEBUG=true  # Должно быть true для разработки
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contest_platform  # Название базы данных
DB_USERNAME=root              # Пользователь MySQL
DB_PASSWORD=                  # Пароль (пустой для XAMPP)

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=minioadmin      # Логин MinIO
AWS_SECRET_ACCESS_KEY=minioadmin  # Пароль MinIO
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=contests                # Название bucket'а
AWS_ENDPOINT=http://localhost:9000 # API endpoint MinIO
AWS_USE_PATH_STYLE_ENDPOINT=true   # Важно для MinIO!

QUEUE_CONNECTION=database  # Используем БД для очередей

8. # ЗАПУСК MinIO ЧЕРЕЗ DOCKER

# Создайте папку для данных MinIO (если нет)
mkdir C:\minio-data

9. # Создайте папку для данных MinIO (если нет)
mkdir C:\minio-data

10. # Запустите контейнер
docker run -d \
  --name minio \
  -p 9000:9000 \
  -p 9001:9001 \
  -v C:/minio-data:/data \
  -e "MINIO_ROOT_USER=minioadmin" \
  -e "MINIO_ROOT_PASSWORD=minioadmin" \
  minio/minio server /data --console-address ":9001"

11. Откройте веб-интерфейс MinIO:

Перейдите в браузере: http://localhost:9001

Логин: minioadmin

Пароль: minioadmin

Создайте bucket:

Нажмите кнопку "Create Bucket" (справа вверху)

Введите имя: contests (точно как в .env файле)

Нажмите "Create Bucket"

Проверьте создание bucket'а:

В списке bucket'ов должен появиться contests

12. # Зайдите в консоль Laravel
php artisan tinker

13. # Выполните проверку
>>> Storage::disk('s3')->put('test.txt', 'Hello MinIO!');
=> true
>>> Storage::disk('s3')->exists('test.txt');
=> true
>>> Storage::disk('s3')->delete('test.txt');
=> true
>>> exit

14. Создание таблиц (миграции)

# Создаем таблицу для очередей (обязательно!)
php artisan queue:table

# Запускаем все миграции
php artisan migrate

# Если нужно пересоздать таблицы (удалит все данные!)
php artisan migrate:fresh

15. # Запускаем сидеры
php artisan db:seed

# Вы должны увидеть сообщение:
# Тестовые пользователи созданы:
# admin@example.com / password
# jury@example.com / password
# participant1@example.com / password
# participant2@example.com / password

16. Настройка Sanctum (для API токенов)

# Публикация конфигурации Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

17. # Запуск

# Перейдите в папку проекта

# Запустите встроенный сервер Laravel
php artisan serve

18. # Запустите воркер очередей
php artisan queue:work

19. # ТЕСТОВЫЕ ПОЛЬЗОВАТЕЛИ
После установки у вас есть 4 тестовых пользователя:

Роль|Имя|Email|Пароль
Администратор|Admin User|admin@example.com|password
Жюри|Jury User|jury@example.com|password
Участник 1|Participant One|participant1@example.com|password
Участник 2|Participant Two|participant2@example.com|password




