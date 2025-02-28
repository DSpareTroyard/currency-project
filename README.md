# Currency Widget Application

Приложение для отображения и обновления курсов валют ЦБ РФ с использованием:
- CakePHP 4 (бэкенд)
- React (фронтенд)
- MySQL (база данных)
- Redis (кэширование)
- Docker (контейнеризация)

## Требования
- Docker 20.10+
- Docker Compose 1.29+
- 4 GB свободной памяти
- Порты 8000, 3000, 3306 свободны

## Установка и запуск

### 1. Клонирование репозитория
git clone https://github.com/DSpareTroyard/currency-project.git  
cd currency-widget

### 2. Настройка окружения
Создайте файл .env в корне проекта:  
DB_HOST=db  
DB_USER=root  
DB_PASSWORD=secret  
DB_NAME=currency  
REDIS_HOST=redis  
REDIS_PORT=6379  

### 3. Запуск контейнеров
docker-compose up --build -d

### 4. Установка зависимостей
#### Установка PHP зависимостей
docker-compose exec api composer install

#### Установка Node.js зависимостей
В директории frontend выполните
npm install

### 5. Создание необходимых директорий
docker-compose exec api mkdir -p tmp/logs  
docker-compose exec api chmod -R 777 tmp

### 6. Настройка app_local.php
Добавьте в backend/config/app_local.php:
```json
'Cache' => [
    'redis' => [
        'className' => 'Cake\Cache\Engine\RedisEngine',
        'host' => 'redis',
        'port' => 6379,
        'prefix' => 'currency_',
        'duration' => '+5 minutes',
        'persistent' => true
    ]
]
// ...
```
```json
'Cors' => [
    'AllowOrigin' => ['http://localhost:3000'],
    'AllowMethods' => ['GET'],
    'AllowHeaders' => ['Content-Type'],
    'AllowCredentials' => true,
    'ExposeHeaders' => [],
    'MaxAge' => 300
]
// ...
```
```json
Измените Datasources
'Datasources' => [
    'default' => [
        'host' => 'db',
        'username' => 'root',
        'password' => 'secret',
        'database' => 'currency',
        // ... остальные настройки оставьте по умолчанию
    ],
]
// ...
```

### 7. Инициализация приложения
Создание структуры БД
docker-compose exec api bin/cake migrations migrate

Наполнение начальными данными
docker-compose exec api bin/cake currency_fetch

Доступ к приложению

Фронтенд: http://localhost:3000
Бэкенд (API): http://localhost:8000

## Примеры запросов к API

### Получить список всех валют
GET /api/currencies.json

### Обновить курс валют
POST /api/update-rates
