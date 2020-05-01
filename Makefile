dc_path=docker/docker-compose.yml
app_container=symfony_app
db_container=db

init: up init_script user cc beacons

up:
	docker-compose -f ${dc_path} up -d --build
	docker-compose -f ${dc_path} exec ${app_container} ./chown.sh

down:
	docker-compose -f ${dc_path} down

rebuild:
	@/bin/echo -n "All the volumes will be deleted. You will loose data in DB. Are you sure? [y/N]: " && read answer && \
	[[ $${answer:-N} = y ]] && make destroy && make init

destroy:
	docker-compose -f ${dc_path} down --volumes --remove-orphans
	docker-compose -f ${dc_path} rm -vsf

jumpapp:
	docker-compose -f ${dc_path} exec --user=www-data ${app_container} bash

jumpdb:
	docker-compose -f ${dc_path} exec --user=www-data ${db_container} mysql

demo: # Loads demo data to the DB
	curl https://s3.eu-central-1.amazonaws.com/com.basicrum.demo/test_data/may-july-2019.sql.gz -o may-july-2019.sql.gz
	gunzip -c may-july-2019.sql.gz | docker-compose -f ${dc_path} exec -T ${db_container} sh -c 'mysql $${MYSQL_DATABASE}'
	rm may-july-2019.sql.gz
	make cc
	make beacons

test:
	docker-compose -f ${dc_path} exec --user=www-data ${app_container} ./bin/phpunit

init_script:
	docker-compose -f ${dc_path} exec -T --user=www-data ${app_container} ./init.sh

cc:
	docker-compose -f ${dc_path} exec -T --user=www-data ${app_container} ./bin/console cache:clear
	docker-compose -f ${dc_path} exec -T --user=www-data ${app_container} ./bin/console basicrum:cache:clean

user: # Creates superadmin user
	docker-compose -f ${dc_path} exec --user=www-data ${app_container} ./bin/console basicrum:superadmin:create

beacons: # Prepare beacons folders
	docker-compose -f ${dc_path} exec -T --user=www-data ${app_container} ./bin/console basicrum:beacon:init-folders

migrate:
	docker-compose -f ${dc_path} exec --user=www-data ${app_container} ./bin/console doctrine:migrations:migrate
