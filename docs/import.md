# Data Import

As mentioned the HealthCheck application imports data from MiData. This import procedure consists of multiple steps. 
First the data is fetched from the MiData endpoints, then imported into our database and finally aggregated 
by collecting all needed information for the front-end and storing it inside separate tables. 
This logic is split up into three commands: `App\Command\FetchDataCommand`, `App\Command\ImportFromJsonCommand`, `App\Command\AggregateCommand`.
This whole procedure is error prone therefore we cannot allow any wrong/bad data to be imported or aggregated.
For that reason these commands are surrounded by try catch blocks, and we immediately stop if we encounter exceptions. 
For importing and aggregating we have transactions in place to handle any SQL/Doctrine failures and exceptions.

## Content
- [Cronjob](#cronjob)
- [Fetching Data](#fetching-data)
- [Importing Data](#importing-data)
- [Aggregating Data](#aggregating-data)
- [Logging](#logging)

## Cronjob

The application will fetch, import and aggregate data every day at 02:00 in the morning. 
There is a cronjob which will execute a bash script, which then will run each command. 
The cronjob can be found in the root of the application `./crontab` and the script which will run is also in the project 
root `./run-import.sh`.

## Fetching Data

This command GETs data from MiData Endpoints using an API Key defined in the environment variable `PBS_API_KEY`. 
The base path is assigned to the `PBS_DATA_URL` environment variable. All data fetched by this command is stored in 
the path configured in the `IMPORT_TARGET_DIR` environment variable. The specified directory will be prefixed with 
the project path, this can be seen inside `config/services.yml` where an `import_data_dir` is defined and prefixed 
with the `kernel.project_dir` provided by Symfony:

```yml
...
import_data_dir: '%kernel.project_dir%/%env(IMPORT_TARGET_DIR)%'
...
services:
...
    # Commands
    App\Command\FetchDataCommand:
        arguments:
            $importDirectory: '%import_data_dir%'
...
```
At the beginning of this command the specified target directory will be checked and if any files exist they will be 
removed so there is no conflict with previous imports.

### Requesting MiData API Endpoints

To make requests to the MiData API the `App\Service\PbsApiService` is used from the command.

```php
/**
 * @param string $tableName
 * @param int|null $page
 * @param int|null $itemsPerPage
 * @return Http\CurlResponse
 */
public function getTableData(string $tableName, int $page = null, int $itemsPerPage = null) {
    $endpoint = $this->url . '/group_health/' . $tableName;
    if ($page !== null && $itemsPerPage !== null) {
        $endpoint .= '?page=' . $page . '&size=' . $itemsPerPage;
    }
    $additionalHeaders = ['X-Token' => $this->apiKey];
    return $this->guzzleWrapper->getJson($endpoint, null, $additionalHeaders);
}
```

As visible above, the path is always in the following format: `<PBS_DATA_URL>/group_health/<TABLE_NAME>`. 
Additionally, some endpoints are paginated and there we have additional URL params like `?page=<PAGE>&size=<ITEMS_PER_PAGE>`. 
The pagination is handled by the command since there are some paginated and some non paginated endpoints. 
All paginated and non-paginated endpoints are defined in the command class:

```php
private const PAGINATED = ['people', 'groups', 'roles', 'courses', 'camps', 'participations', 'qualifications'];
private const NOT_PAGINATED = ['group_types', 'role_types', 'participation_types', 'j_s_kinds', 'camp_states',
        'qualification_kinds', 'event_kinds'];
```

### Response Format

The API responses for all endpoints return an object containing the table as object array in the table name property:

```json
{ "<TALE_NAME>": [ {}, ... ] }
```

### Storing request data

The command will create a json file for each endpoint where it stores all entities with this naming scheme `<TABLE_NAME>.json`. 
For non paginated endpoints the response is simply encoded into JSON and written directly to the created file since 
these endpoints do not serve large amounts of data. For paginated endpoints this is problematic since we have to take 
memory usage into consideration, we simply cannot add up all items into an array and then `json_encode()` them. 
Instead we create the json file for the table and move the file pointer to the end of the json array inside the file and 
then only encode and append each fetched page to the end of the file. This is much more memory efficient and can be seen 
in the `appendJsonToFile()` function inside the command.

### Running the fetch command

To execute this command simply run `php bin/console app:fetch-data`. If you are using the docker-compose configuration 
you need to run the command inside the `healthcheck-core` service container.

## Importing Data

This command reads and de-serializes all data from the JSON files created by the `app:fetch-data` command and then finally 
inserts the data into the specific table. Relevant tables for this import are prefixed with `midata_`. 
  
### Doctrine Batch Processing

Since we are working with a large data set this command has to run a lot of queries. 
In Symfony the Doctrine `EntityManager` is used to persist the entities that are stored. 
Calling `$entityManager->flush()` every time after `$entityManager->persist($entity)` is very expensive since doctrine 
does not directly run the query when persisting but only executes it on flush. 
There is a lot of overhead if we run flush every time we persist. To solve this problem we can use doctrine batch processing. 
This simply means that we run flush after persisting a defined number of entities, this will drop the overhead and 
dramatically increase performance. The batch size is defined in the command as `$batchSize`.

### Running the import command

To execute this command simply run `php bin/console app:import-data`. If you are using the docker-compose configuration 
you need to run the command inside the healthcheck-core service container.

## Aggregating Data

This command aggregates all data that was imported into specific tables. The purpose of this aggregation procedure is to 
maximize efficiency in regard to fetching the data for visualization. Computing all the needed data for the charts/widgets 
in the front-end would take way too long since it is a large dataset that will only grow over time.

### Data Historization

In order to visualize the data that was imported for specific time periods or dates a kind of "history" of the data needs 
to be created. This means that in the aggregation data is aggregated for a specific point in time. 
This process is repeated for each date that needs to be aggregated.

#### Example for better understanding

The imported data contains `created_at` and `deleted_at` attributes for relevant entities. 
With these attributes we can basically look back at how the data for a specific entity was at a given point in time. 
For example if we take the `Person` entity we have the following useful fields:

```php
/**
 * @ORM\Column(type="datetime_immutable", nullable=true)
 */
private $entryDate;

/**
 * @ORM\Column(type="datetime_immutable", nullable=true)
 */
private $leavingDate;
```

Now let's say we would want to check how many people there were for a given date in the past (01.01.2015) then we can 
simply `SELECT * FROM people WHERE entry_date < 2015-01-01 AND leaving_date > 2015-01-01`.

The application does exactly this for every month and finally the current date. 
The earliest available date is chosen from the needed entities specific to the aggregation. 
Next the data in the aggregation is collected for every first day of the month. 
So for example we start with 01.01.2013 as the earliest available date and the we do this aggregation procedure 
for 01.01.2013, 01.02.2013, 01.03.2013 , ..., and finally for the current date as well. 
In the aggregation tables there is a `data_point_date` field which will hold these dates that we aggregate data for.

This also brings an advantage. The aggregation only has to be done once after that we only need to compute aggregations 
for the newly imported data since the data of the past doesn't change. If a person left the organisation a few years 
ago the newly imported data won't change that and therefore we do not have to recompute for already aggregated dates.

### Aggregation Structure

Each widget/chart requires some specific logic for its aggregation therefore this part of the application needs to be 
somewhat dynamic. There is an abstract `WidgetAggregator` class which can be extended by every new aggregator class 
for specific widgets. Each aggregator has a name which is used to identify it. 
This can all be nicely seen when checking the `App\Service\Aggregator` namespace under `src/Service/Aggregator/`.

As visible in the `WidgetAggregator` class only 3 functions need to be implemented from the abstract class.

```php
abstract class WidgetAggregator
{
    abstract public function getName();
    abstract public function aggregate();
    abstract public function isDataExistsForDate(string $date, array $data);
}
```

`aggregate()` will take care of the aggregation logic, `isDataExistsForDate()` is used to check if we already aggregated 
data for a given date and as mentioned before `getName()` will be used to identify the aggregator class.

In addition to these aggregation classes there is the `App\Service\Aggregator\AggregatorRegistry`. 
This will hold all the aggregators in an array so one can simply iterate over them and call them as needed. 
For this kind of pattern the application takes advantage of the Symfony framework. 
Inside `config/services.yml` we can see how dependency injection takes care of this as we need it:

```yml
services:
    ...
    App\Service\Aggregator\DemographicGroupAggregator:
        tags:
            - { name: 'widget.aggregator', key: 'widget.demographic-group' }

    App\Service\Aggregator\DemographicCampAggregator:
        tags:
            - { name: 'widget.aggregator', key: 'widget.demographic-camp' }

    App\Service\Aggregator\AggregatorRegistry:
        arguments: [!tagged { tag: 'widget.aggregator', index_by: 'key' }]
```

Finally, the `AggregatorRepository` is simply injected inside the `AggregateCommand` and now iterating and 
calling each aggregator becomes easy and dynamic:

```php
/**
 * @param InputInterface $input
 * @param OutputInterface $output
 * @return int
 * @throws \Doctrine\DBAL\ConnectionException
*/
protected function execute(InputInterface $input, OutputInterface $output)
{
    ...
    try {
        ...
        $aggregators =  $this->aggregatorRegistry->getAggregators();
        /** @var WidgetAggregator $aggregator */
        foreach ($aggregators as $aggregator) {
            ...
            $aggregator->aggregate();
            ...
        }
        ...
    } catch (\Exception $e) {
        ...
    }
    ...
}
```

### Running the aggregate command

To execute this command simply run `php bin/console app:aggregate-data`. If you are using the docker-compose configuration 
you need to run the command inside the healthcheck-core service container.

## Logging

The application records some details on command execution and sends them to a log server.

### Important
**No application specific/user data is collected in these commands!** This data is only to make sure the application has 
enough resources and can run properly without failing. Addtionally, this information might be useful for debugging import failures. 

You do not need to add a logging server to run the application locally, it will run without it since logger 
exceptions are silenced. Just leave the following env vars from `.env.dist` empty:
```dotenv
GRAYLOG_HOST=
GRAYLOG_PORT=
GRAYLOG_CLIENT_CERT=
GRAYLOG_CLIENT_KEY=
``` 

The import commands all implement the `StatisticsCommand` interface. This forces them to implement the `getStats()` method. 
Symfony dispatches useful command related events that the application listens to. This can be seen in `config/services.yml`:

```yml
services:
...
    # Listeners
    App\EventListener\ConsoleEventListener:
        tags:
            - { name: kernel.event_listener, event: console.command }
            - { name: kernel.event_listener, event: console.terminate }
            - { name: kernel.event_listener, event: console.error }
```

Symfony will call the `ConsoleEventListener` when any command starts, ends or fails. 

### On Command Start

When a command start we send a `CommandStartLogMessage` to the logger which simply contains the name command which will be run. 
This happens inside the `onConsoleCommand()` method inside `ConsoleEventListener`.

### On Command End

When a command finishes successfully the `onConsoleTerminate()` method will be called inside the `ConsoleEventListener`. 
There we can now get the executed command from the `ConsoleTerminateEvent` which is passed to the method and collect the 
processing details which the commands return in the implemented `getStats()` method.

A `StatisticsCommandMessage` is sent to the logger which is constructed inside the command that was executed. 
It contains the execution time, peak memory usage and an array with other command specific details.

### On Command Error

When an exception is thrown during command execution the `onConsoleError()` inside `ConsoleEventListener` will be called. 
Here we simply create a `ExceptionLogMessage` and send it to the graylog server.