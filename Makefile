
DOCKER_COMPOSE_COMMAND=$(if $(shell docker compose 2>/dev/null),docker compose,docker-compose)
DOCKER_COMPOSE_FILE=-f ./docker/docker-compose.yml
ERROR_LOG_FILE=var/log/errors.log


.PHONY: pull
pull:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) pull

.PHONY: build
build:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) build

.PHONY: build-debug
build-debug:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) build --build-arg="BUILD_DEBUG=1"

.PHONY: build-test
build-test:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) build --build-arg="BUILD_TEST=1"

.PHONY: setup
setup:
	make build
	./scripts/install_dependencies_locally.sh
	make up
	sleep 5
	docker exec healthcheck-core-local /usr/local/bin/php /srv/bin/console doctrine:migrations:migrate

.PHONY: up
up:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) up -d

.PHONY: down
down:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) down

.PHONY: reload-env
reload-env:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) down healthcheck-core
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) up healthcheck-core -d

.PHONY: import
import:
	docker exec healthcheck-core-local ./run-import.sh

.PHONY: test
test:
	make down
	docker compose -p healthcheck-test -f docker/docker-compose.yml build --build-arg BUILD_TEST=1 healthcheck-core
	docker compose -p healthcheck-test -f docker/docker-compose.yml up -d
	docker exec healthcheck-core-local composer install --no-interaction --no-scripts
	docker exec healthcheck-core-local php vendor/bin/phpcs --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/
	docker exec healthcheck-core-local php bin/phpunit || true
	docker compose -p healthcheck-test -f docker/docker-compose.yml down

.PHONY: lint
lint:
	docker exec healthcheck-core-local php vendor/bin/phpcs --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/
	docker exec healthcheck-core-local php bin/console lint:twig templates

.PHONY: lint\:fix
lint\:fix:
	docker exec healthcheck-core-local php vendor/bin/phpcbf --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/
	docker exec healthcheck-core-local php bin/console lint:twig templates

.PHONY: logs
logs:
	echo "\n\n\nListening to errors...\n\n" >> $(ERROR_LOG_FILE)
	tail -f $(ERROR_LOG_FILE)