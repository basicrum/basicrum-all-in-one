.PHONY: help
.DEFAULT_GOAL := help
SHELL=bash

REPO := basicrum/backoffice_php_fpm
TAG := $(shell git tag --points-at HEAD | head -1)

dc_path=docker/docker-compose.yml
app_container=symfony_app
db_container=db

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: up init_script user cc beacons ## Initialise environment on a first start

up: ## Start local environment
	docker-compose -f ${dc_path} up -d --build

down: ## Stop local environment
	docker-compose -f ${dc_path} down

restart: down up # Restart environment

rebuild: ## Rebuild local environment from scratch
	@/bin/echo -n "All the volumes will be deleted. You will loose data in DB. Are you sure? [y/N]: " && read answer && \
	[[ $${answer:-N} = y ]] && make destroy && make init

destroy: ## Destroy local environment
	docker-compose -f ${dc_path} down --volumes --remove-orphans
	docker-compose -f ${dc_path} rm -vsf

jumpapp: ## Jump to the app container
	docker-compose -f ${dc_path} exec ${app_container} bash

jumpdb: ## Jump to db container
	docker-compose -f ${dc_path} exec ${db_container} mysql

demo: ## Loads demo data to the DB
	curl https://s3.eu-central-1.amazonaws.com/com.basicrum.demo/test_data/may-july-2019.sql.gz -o may-july-2019.sql.gz
	gunzip -c may-july-2019.sql.gz | docker-compose -f ${dc_path} exec -T ${db_container} sh -c 'mysql $${MYSQL_DATABASE}'
	rm may-july-2019.sql.gz
	make cc
	make beacons

test: ## Start tests on local environment
	docker-compose -f ${dc_path} exec ${app_container} ./bin/phpunit

init_script: ## Inistall dependencies and apply migrations
	docker-compose -f ${dc_path} exec -T ${app_container} ./init.sh

cc: ## Clean cache
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console cache:clear
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console basicrum:cache:clean

user: ## Creates superadmin user
	docker-compose -f ${dc_path} exec ${app_container} ./bin/console basicrum:superadmin:create

beacons: ## Prepare beacons folders
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console basicrum:beacon:init-folders

migrate: ## Apply migrations
	docker-compose -f ${dc_path} exec ${app_container} ./bin/console doctrine:migrations:migrate

docker_publish: docker_build docker_login docker_push ## Publish new image to docker hub

docker_build: # Build new docker image
	cat docker/symfony_app/Dockerfile > docker/symfony_app/Dockerfile.prod
	cat docker/symfony_app/Dockerfile.prod.patch >> docker/symfony_app/Dockerfile.prod
	docker build --no-cache -t ${REPO}:${TAG} -f docker/symfony_app/Dockerfile.prod .
	rm docker/symfony_app/Dockerfile.prod

docker_push: # Push new docker image to docker hub
	docker push ${REPO}:${TAG}

docker_login: # Login to docker hub
	docker login -u ${DOCKER_USERNAME} -p ${DOCKER_PASSWORD}
