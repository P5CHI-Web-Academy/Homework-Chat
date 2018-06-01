#!/bin/bash

docker-compose up -d
docker-compose exec php bash -c "php vendor/bin/doctrine orm:schema-tool:create -q"
docker-compose exec php bash -c "php vendor/bin/doctrine orm:schema-tool:update --force"
docker-compose exec php bash -c "php bin/ChatServer.php"
