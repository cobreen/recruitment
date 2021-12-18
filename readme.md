# Setup
App needs ports `5432` and `8080` to run.
Copy `.env.example` into `.env`
Run:
- `docker-compose up -d`
- `docker exec -it php-apache /bin/bash`
- `composer install`
- `php artisan migrate`


# usage:
The aplication is API only
API can be easily tested by using postman
Use `src\TestTaskApi.postman_collection.json` to test API
