init: composer-update npm-update up

up:
	docker-compose up -d
down:
	docker-compose down

composer:
	docker-compose run php composer $(filter-out $@, $(MAKECMDGOALS))
composer-update:
	docker-compose run php composer update
	npm i bootstrap@5.3.1

npm:
	docker-compose run php npm $(filter-out $@, $(MAKECMDGOALS))
npm-update:
	docker-compose run php npm update
