#!/bin/bash

npm install
composer install
docker-compose up -d
docker-compose exec php-fpm bash -c "bin/console doctrine:schema:create -q"
docker-compose exec php-fpm bash -c " chmod -R 777 public/uploads"
