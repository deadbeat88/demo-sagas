#!/usr/bin/env bash

BASEDIR=$(dirname $0)

cd ${BASEDIR}/..

docker-compose build --build-arg userid=$(id -u)