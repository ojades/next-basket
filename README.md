## Next Basket Application Test

This solution contains two applications as requested based on Laravel 10 and php 8.2 with Apache web server. The application service are:

- Users.
- Notifications.

Other servies included as needed are:

- Mysql Database
- RabbitMq

### Getting Started

Clone the repository

`git clone`

You can get started easily by starting up with docker

`$ cd next-basket`

make a copy of .env.exmple in each of `users` and `notifications` directories and change to match your configuration

`$ cp .env.example .env`

Configurations you'll need for the applications are:

```
APP_URL=http://localhost:89

MQ_HOST=rabbitmq
MQ_PORT=5672
MQ_USER=rabbitmq
MQ_PASS=rabbitmq
MQ_VHOST="/"

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=users_app
DB_USERNAME=db_user
DB_PASSWORD=uz3rp@@s
```

You can change any of these defaults, but you'll have to modify same in the `docker-compose.yml` configuration file.

`$ docker compose up --build -d`

This will pull the necessary images and build up the containers for each service.
To confirm that everything went well, run

`docker ps` to see all running containers

```
❯ docker ps
CONTAINER ID   IMAGE             COMMAND                  CREATED          STATUS          PORTS                                                                                                                           NAMES
cd42fcf8d72a   mysql:5.7.22      "docker-entrypoint.s…"   27 seconds ago   Up 22 seconds   0.0.0.0:3306->3306/tcp                                                                                                          db
e4c3e1e1901e   notifications     "docker-php-entrypoi…"   27 seconds ago   Up 24 seconds   0.0.0.0:90->80/tcp                                                                                                              notifications
333d415336ef   users             "docker-php-entrypoi…"   27 seconds ago   Up 22 seconds   0.0.0.0:89->80/tcp                                                                                                              users
344d67304f4b   rabbitmq:3.8.14   "docker-entrypoint.s…"   27 seconds ago   Up 22 seconds   0.0.0.0:4369->4369/tcp, 0.0.0.0:5671-5672->5671-5672/tcp, 0.0.0.0:15672->15672/tcp, 0.0.0.0:25672->25672/tcp, 15691-15692/tcp   rabbitmq
```

### Using The Application

`{users_service_base_url} - localhost:89`

`{notifications_service_base_url} - localhost:90`

If you have specified a different port binding the docker-compose.yml file, use that in your url.

Post User:
`[POST] /api/users`

Body:

```
{
    "email": "jdoe@mail.com",
    "first_name": "John",
    "last_name": "Doe"
}
```

Sample response:

```
{
    "successful": true,
    "data": {
        "email": "testmail11@gmail.com",
        "first_name": "Terry",
        "last_name": "Vela"
    },
    "message": "Queued succefully"
}
```

Date is stored in connected databasee on users service.

Then notification message consumed by notification service can be seen at

`/notifications/storage/logs/laravel.log`

### Tests

To run tests, simple run

`docker exec users php artisan test`
