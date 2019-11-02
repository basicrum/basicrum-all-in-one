ifndef REPO
	REPO = "basic_rum"
endif
ifndef CODE_TAG
	override CODE_TAG =  $(shell git rev-parse HEAD)
endif

.PHONY: run build build_run

run:
	$(info Starting dev environment.)
	docker-compose -f docker/docker-compose.yml -f docker/docker-compose-local.yml up -d --build

build:
	$(info Starting dev environment.)
	docker build -f docker/Dockerfile -t ${REPO}:${CODE_TAG} --build-arg BASEDIR="." .

run-image:
	export CODE_IMAGE="${REPO}:${CODE_TAG}"
	docker-compose -f docker/docker-compose.yml -f docker/docker-compose-image.yml run -d fpm

build-run: kill build run-image

stop:
	docker-compose stop -f docker/docker-compose.yml

kill:
	docker-compose rm -f docker/docker-compose.yml