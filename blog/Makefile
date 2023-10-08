init: composer-update npm-update up

up:
	docker-compose up -d
down:
	docker-compose down

composer:
	docker-compose run php composer $(filter-out $@, $(MAKECMDGOALS))
composer-update:
	docker-compose run php composer update

npm:
	docker-compose run php npm $(filter-out $@, $(MAKECMDGOALS))
npm-update:
	docker-compose run php npm update

yii3:
	docker-compose run php ./yii $(filter-out $@, $(MAKECMDGOALS))

test:
	docker-compose run php ./vendor/bin/codecept run
psalm:
	docker-compose run php ./vendor/bin/psalm
