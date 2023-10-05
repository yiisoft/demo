up:
	docker-compose up -d
down:
	docker-compose down

composer:
	docker-compose run php composer $(filter-out $@, $(MAKECMDGOALS))
