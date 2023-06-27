
DOCKER_COMPOSE_COMMAND=$(if $(shell docker compose 2>/dev/null),docker compose,docker-compose)
DOCKER_COMPOSE_FILE=-f ./docker/docker-compose.yml


.PHONY: pull
pull:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) pull

.PHONY: build
build:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) build

.PHONY: build-debug
build-debug:
	$(DOCKER_COMPOSE_COMMAND) $(DOCKER_COMPOSE_FILE) build --build-arg="BUILD_DEBUG=1"

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

.PHONY: import
import:
	docker exec healthcheck-core-local ./run-import.sh
