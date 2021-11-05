#! /bin/bash

# exit when any command fails
set -e

# Run import commands from symfony project
/usr/local/bin/php /srv/bin/console app:fetch-data
/usr/local/bin/php /srv/bin/console app:import-data
/usr/local/bin/php /srv/bin/console app:import-questionnaire
/usr/local/bin/php /srv/bin/console app:map-peoples-addresses
/usr/local/bin/php /srv/bin/console app:aggregate-data
