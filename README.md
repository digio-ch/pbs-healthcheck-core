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

This guide should help you get the project up and running locally on any machine. For more information on the application, read the application docs in [`doc/index.md`](docs/index.md).

It is highly recommended that you run this application with Docker and docker-compose as described here. We will not provide any additional guides for running the application without Docker or with local php/postgres installations.

### Prerequisites
You need Git and Docker installed on your system. Docker needs to be up-to-date so it supports at least docker-compose version 3.7.

Make sure ports 4200, 5432, 5441, 8000 and 9000 are not in use on your machine.

### Step 1: Get necessary credentials from MiData test environment
* [x] Go to https://pbs.puzzle.ch
* [x] Log in as an admin using `hussein_kohlmann@hitobito.example.com` with password `hito42bito`
* [x] Go to Einstellungen (in the left side navigation) -> OAuth-Applikationen -> HealthCheck - Testsystem (https://pbs.puzzle.ch/de/oauth/applications/19 - or create a new OAuth application with email, name and with_roles scopes and with http://localhost:4200/callback as the callback URI).
  * :arrow_right: The "Uid" value will be referenced as `[1]` in environment.ts and .env below.
  * :arrow_right: The "Secret" value will be referenced as `[2]` in .env below.
* [x] Go to Pfadibewegung Schweiz -> Einstellungen (the tab on the top right) -> API-Keys -> HealthCheck - Testsystem (https://pbs.puzzle.ch/de/groups/1/service_tokens/45 or create a new service token with HealthCheck permission).
  * :arrow_right: The "Token" value will be referenced as `[3]` in .env below.

### Step 2: Get the frontend up and running
* [x] Open a console (bash or a Terminal or something similar) and type the following commands:
```bash
# Download the frontend code
git clone https://github.com/digio-ch/pbs-healthcheck-web
# Go to the downloaded code
cd pbs-healthcheck-web
```

* [x] Edit src/environments/environment.ts and fill in the following info:
```
    clientId: '[1]', // insert the value as described above, between the '' quotes
```

* [x] Again in the console, run the following commands:
```bash
# Build and start the frontend container
docker-compose -f docker/docker-compose.yml up -d
# Install third-party software that's needed into the container
docker exec healthcheck-web-local yarn install
# Start the web server process inside the container
docker exec healthcheck-web-local yarn run start --host 0.0.0.0
```

### Step 3: Get the backend up and running and import data from MiData test environment
* [x] Open a separate new console for the backend, keeping the first one running
* [x] Run the following commands:
```bash
# Download the backend code
git clone https://github.com/digio-ch/pbs-healthcheck-core
# Go to the downloaded code
cd pbs-healthcheck-core
```
* [x] Create a copy of .env.dist and name it .env (a period and env, all lower case)
* [x] Edit the new .env file and fill in the following info in the corresponding sections:
```
# OAuth 2.0
PBS_URL=https://pbs.puzzle.ch
PBS_CLIENT_ID=[1] (insert the value from pbs.puzzle.ch as described above, this time without any quotes)
PBS_CLIENT_SECRET=[2] (insert the value from pbs.puzzle.ch as described above, this time without any quotes)
PBS_CALLBACK_URL=http://localhost:4200/callback
SPECIAL_ACCESS=

# PBS
PBS_API_KEY=[3] (insert the value from pbs.puzzle.ch as described above, this time without any quotes)
PBS_DATA_URL=https://pbs.puzzle.ch
```
* [x] Again in the backend console, run the following commands:
```bash
# Build the backend container with some special needed options
docker-compose -f docker/docker-compose.yml build --build-arg BUILD_TEST=1 healthcheck-core
# Start the backend container and the database and other useful services
docker-compose -f docker/docker-compose.yml up -d
# Install third-party software that's needed into the container
docker exec healthcheck-core-local composer install --no-interaction --no-scripts
# Set up the database structure
docker exec healthcheck-core-local php bin/console doctrine:migrations:migrate -n
```
* [x] In a browser, go to https://pbs.puzzle.ch and log out, then log in as an AL using `letizia_wilhelm@hitobito.example.com` with password `hito42bito`
* [x] Edit an Abteilung where you have an AL role and activate the HealthCheck Opt-In flag (if it isn't already activated)
* [x] In the second (backend) console, run the following command to manually trigger the nightly import. This will import all opted-in Abteilungen and might take a few minutes, especially the aggregations.
```bash
docker exec healthcheck-core-local ./run-import.sh
```
* [x] Go to http://localhost:4200 and click the "Anmelden via MiData" button.

### After you're done: Stopping the containers
* [x] In the frontend console, press <kbd>Ctrl</kbd> + <kbd>C</kbd>
* [x] In the frontend console, run `docker-compose -f docker/docker-compose.yml down`
* [x] In the backend console, run `docker-compose -f docker/docker-compose.yml down`

You can then close both consoles.

### More info

#### Docker build

The backend docker build also accepts an argument `BUILD_DEBUG=1`, which will add xdebug to it to ease development/debugging. `BUILD_TEST=1` will add composer to the image, which is required in the steps outlined above.

If you are gettings any docker network errors make sure that the subnet defined inside the `docker/docker-compose.yml` does not conflict with any of your existing networks.

#### Code Format Checking

We use the PSR-12 PHP standard. You can check your code using the following command:

`docker exec healthcheck-core-local php vendor/bin/phpcs --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/`

Auto linting:
`docker exec healthcheck-core-local php vendor/bin/phpcbf --standard=PSR12 --report=full --ignore=src/Migrations/ --runtime-set ignore_warnings_on_exit 1 src/`

#### Running Tests

To run tests locally make sure to use the `env.test` instead of the created `.env`. You can do that by replacing the contents of the `.env` file with the contents of the `.env.test` file. You will need to completely stop and restart the healthcheck-core service container in order for the changes to take effect.

Once you are up and running with the new env run:

`docker exec healthcheck-core-local php bin/phpunit`
