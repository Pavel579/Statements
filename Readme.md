#Statements

Приложение для обработки входящих заявок от клиентов.
Авторизация выполнена по JWT токену. Есть 2 роли ROLE_CLIENT и ROLE_ADMIN. Клиенту доступны эндпоинты по /api/clients/, 
админу - /api/admin/. 

Установка:
```bash
composer install
```

В файл .env необходимо прописать актуальные параметры для подключения к БД.
Запуск осуществляется командой:
```bash
docker-compose up
```
Запуск тестов
```bash
docker-compose exec php vendor/bin/phpunit ./tests
```