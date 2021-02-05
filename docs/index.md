# HealthCheck

The HealthCheck Application imports and aggregates data of an external system (MiData) and visualize it usefully. 
The application was developed for the Pfadibewegung Schweiz (short PBS), the swiss national boys and girls scout 
association. PBS provides relevant application data through a JSON REST API and the application imports and aggregates 
the relevant data. Additionally, there is a front-end Angular application which is used to present and visualize 
this data in charts and widgets.

## Contents

- [Remote Dependencies](#remote-dependencies)
- [Authentication](auth.md)
- [Import & Aggregation](import.md)
- [Adding New Widget To Core (Back-End)](adding-new-widgets-core.md)
- [Adding New Widget To Web (Front-End)](adding-new-widgets-web.md)

### Remote Dependencies

These are external application dependencies which are not managed by the creators of the HealthCheck application
but are needed for the application to work.

- hitobito: This is a Product used by multiple institutions and organizations. It consists of an open-source core 
product and extensions which are referred to as 'Wagons'. These so called 'Wagons' allow to extend the functionality of 
the core product. (https://github.com/hitobito/hitobito)
- hitobito_youth and hitobito_pbs: These are 'Wagons' which are maintained by the PBS (Pfadibewegung Schweiz). 
Together with the hitobito core product they build a system called 'MiData'. This systems productive environment can 
be found at https://db.scout.ch. (https://github.com/hitobito/hitobito_pbs and https://github.com/hitobito/hitobito_youth)
- MiData Integration Environment: This is a test environment for the productive MiData application. It is needed for 
local development. Credentials need to be requested since it is not publicly available. (You will need credentials to
configure the OAuth 2.0 in MiData, and an additional API Key to access the import relevant endpoints ont he API)
