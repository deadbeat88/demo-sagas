#!/usr/bin/env bash

rm -rf var/*
mkdir -p var/cache/
chown -R www-data:www-data var/ && chmod -R 0777 var/

sudo -u www-data composer install --optimize-autoloader --no-dev --no-scripts --no-suggest
chown -R www-data:www-data vendor/

sudo -u www-data bin/consumer
