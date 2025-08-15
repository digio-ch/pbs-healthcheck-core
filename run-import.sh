#! /bin/bash

# This script should be scheduled by the cronjob once a day at 2 AM in the morning.
# Change the names of the docker containers according to the environment
# dev -> healthcheck-importer-dev and healthcheck-core-dev
# stage -> healthcheck-importer-stage and healthcheck-core-stage
# prod -> healthcheck-importer-prod and healthcheck-core-prod

# exit when any command fails
set -e

# Run import commands from go importer
docker exec healthcheck-importer-dev pbs-healthcheck-importer -v import

# Run import commands from symfony project
docker exec --user=www-data healthcheck-core-dev /usr/local/bin/php /srv/bin/console app:map-peoples-addresses
docker exec --user=www-data healthcheck-core-dev /usr/local/bin/php /srv/bin/console app:quap:import-questionnaire

# run the aggregator command from the go importer
docker exec healthcheck-importer-dev pbs-healthcheck-importer -v aggregate

docker exec --user=www-data healthcheck-core-dev /usr/local/bin/php /srv/bin/console app:quap:compute-answers

docker exec --user=www-data healthcheck-core-dev /usr/local/bin/php /srv/bin/console app:compute-permissions