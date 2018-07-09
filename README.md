# Bevzenko test work

## Setup

```
composer install
bin/console doctrine:database:create

```

**Built-in web server**

```
php bin/console server:run
```

### Rabbitmq

You can use our rabbitmq docker container.

```
docker-compose -f docker/docker-compose.yml up --build -d
```