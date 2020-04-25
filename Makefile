.PHONY: run build build_run

up:
	docker-compose -f docker/docker-compose.yml up -d --build

rebuild:
	docker-compose -f docker/docker-compose.yml up -d --build --remove-orphans --force-recreate

down:
	docker-compose -f docker/docker-compose.yml down

jumpin:
	docker-compose -f docker/docker-compose.yml exec symfony_app bash
