#!/bin/bash

php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
