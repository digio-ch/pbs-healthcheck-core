# pbs-healthcheck-core

`pbs-healthcheck-core` is the back-end of the HealthCheck Application. 
It consists of a postgres database, pg-admin for easy database exploration, a php-fpm container 
and an additional caddy reverse proxy.

## Links

- [Documentation](docs/index.md)
- [Contribution Guidelines](.github/contributing.md)
- Issue Templates
    - [Bug Report](.github/ISSUE_TEMPLATE/bug-report.md)
    - [Feature Request](.github/ISSUE_TEMPLATE/feature-request.md)
- PR Templates
    - [Bug Fix](.github/PULL_REQUEST_TEMPLATE/bug-fix.md)
    - [Feature Change](.github/PULL_REQUEST_TEMPLATE/feature-change.md)

## Getting Started

This guide should help you get the project up and running locally on any machine. 

### Prerequisites
1. Read the application docs to get an understanding of how things work, you can find them in `doc/index.md`.
2. Make sure your docker installation is up to date so that it supports docker-compose version 3.7 syntax.
3. You will need to request access to the MiData testing/integration environment in order to run the data import 
   procedure and authenticate with OAuth 2.0. 
4. Make sure port 8000, 5432, 5441 and 9000 are not in use

### Docker Setup

The whole HealthCheck application is dockerized, so it can be run independent of the host platform. 
Additionally, you do not need to install anything (php, postgres, ...) everything will run inside the containers. 
It is highly recommended that you run this application with docker and docker-compose we will not provide any additional 
guides for running the application without docker or with local php/postgres installations.

#### Build & Start Services

First you need to set up the environment. To do this you can simply copy the dist env file: `cp .env.dist .env`.
Make sure to add the needed environment variables to the newly created file (`.env`). 
If you are not sure which ones you need I recommend reading the docs starting from `doc/index.md`.

Run the following commands to start the docker-compose services/containers.

```shell script
docker-compose -f docker/docker-compose.yml build --build-arg BUILD_TEST=1 healthcheck-core
docker-compose -f docker/docker-compose.yml up -d
```

You can add some arguments for building the dockerfile for the healthcheck-core service. `BUILD_DEBUG=1` will add 
xdebug to it to ease development/debugging and `BUILD_TEST=1` will add composer to the image.

If you are gettings any docker network errors make sure that the subnet defined inside the `docker/docker-compose.yml`
does not conflict with any of your existing networks:

#### Install Dependencies

This command will only work if you added the `BUILD_TEST=1` build argument since composer is needed to add dependencies.

`docker exec healthcheck-core-local composer install --no-interaction --no-scripts`

#### Set-Up Database

Make sure you execute all the migrations so the schema and tables are in sync with the entities:

`docker exec healthcheck-core-local php bin/console doctrine:migrations:migrate -n`

#### Import Data

To run the import you can execute the `run-import.sh` script inside the healthcheck-core service container. 
Notice: This might take a while to finish.

`docker exec healthcheck-core-local ./run-import.sh`

#### Code Format Checking

We use the PSR-12 PHP standard. You can check your code using the following command:

`docker exec healthcheck-core-local php vendor/bin/phpcs --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/`

#### Running Tests

To run tests locally make sure to use the `env.test` instead of the created `.env`. You can do that by replacing the
contents of the `.env` file with the contents of the `.env.test` file. You will need to completely stop and 
restart the healthcheck-core service container in order for the changes to take effect.

Once you are up and running with the new env run:

`docker exec healthcheck-core-local php bin/phpunit`