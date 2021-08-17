.PHONY: help
.DEFAULT_GOAL := help
SHELL=bash

REPO := basicrum/backoffice_php_fpm
TAG := $(shell git tag --points-at HEAD | head -1)

dc_path=./docker-compose.yml
app_container=symfony_app
crons_container=crons

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: up init_script  ## Initialise environment on a first start

up: ## Starts a local environment
	docker-compose -f ${dc_path} build
	docker-compose -f ${dc_path} up -d

down: ## Stops a local environment
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

jumpcrons: ## Jump to the crons container
	docker-compose -f ${dc_path} exec ${crons_container} bash

test: ## Start tests on local environment
	docker-compose -f ${dc_path} exec ${app_container} ./bin/phpunit

init_script: ## Installs dependencies and applies migrations
	docker-compose -f ${dc_path} exec -T ${app_container} ./init.sh

init_storage: ## Prepare beacons directories
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console basicrum:beacon:init-storage

bundle_raw_beacons: ## Bundle Raw Beacons
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console basicrum:beacon:bundle-raw

import_beacons_bundle: ## Import Beacons
	docker-compose -f ${dc_path} exec -T ${app_container} ./bin/console basicrum:beacon:import-bundle

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
